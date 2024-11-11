<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    VCard,
    VCardTitle,
    VCardText,
    VBtn,
    VDialog,
    VTextField,
    VDataTable,
} from "vuetify/components";
import AppLayout from "@/Layouts/AppLayout.vue";

// Props recebidas do backend
const props = defineProps({ card: Object });

const purchases = ref(props.card.purchases || []);
const modalPurchaseVisible = ref(false);
const modalInstallmentVisible = ref(false);
const isEditingPurchase = ref(false);
const selectedPurchase = ref(null);
const categories = ref([]);

const fetchEnums = async () => {
    try {
        const categoryResponse = await axios.get("/enums/category");
        categories.value = categoryResponse.data.map((item) => ({
            value: item.value,
            label: item.label,
        }));
    } catch (error) {
        console.error("Erro ao buscar enums:", error);
    }
};

const formattedPurchases = computed(() => {
    return purchases.value.map((purchase) => ({
        ...purchase,
        amount: parseFloat(purchase.amount).toFixed(2).replace(".", ","),
        purchase_date: new Date(purchase.purchase_date).toLocaleDateString(
            "pt-BR"
        ),
        category:
            categories.value.find(
                (category) => category.value === purchase.category
            )?.label || purchase.category,
    }));
});

const purchaseForm = useForm({
    description: "",
    amount: "",
    purchase_date: "",
    category: "",
    installments_count: "",
    credit_card_id: props.card.id,
});

const installmentForm = useForm({
    amount: "",
    due_date: "",
    status: "",
    purchase_id: "",
});

// Abrir modal para nova compra
const openCreatePurchaseModal = () => {
    resetPurchaseForm();
    isEditingPurchase.value = false;
    modalPurchaseVisible.value = true;
};

const formattedCategories = computed(() => {
    return categories.value.map((item) => ({
        value: item.value,
        title: item.label,
    }));
});

// Resetar formulário de compra
const resetPurchaseForm = () => {
    purchaseForm.description = "";
    purchaseForm.value = "";
    purchaseForm.purchase_date = "";
    purchaseForm.due_date = "";
};

// Submeter formulário de compra
const submitPurchaseForm = () => {
    purchaseForm.post(route("credit-card-purchases.store"), {
        onSuccess: () => {
            // Atualizar a lista de compras com a nova compra
            purchases.value.push(purchaseForm.data);
            modalPurchaseVisible.value = false;
            resetPurchaseForm();
        },
        onError: () => {
            console.log("Erro: ", purchaseForm.errors);
        },
    });
};

// Pagar parcela
const payInstallment = (installment) => {
    installmentForm.amount = installment.amount;
    installmentForm.due_date = installment.due_date;
    installmentForm.status = true;
    installmentForm.purchase_id = installment.purchase_id;
    installmentForm.put(route("purchase-installments.update", installment.id), {
        onSuccess: () => {
            // Atualizar a parcela na lista de parcelas
        },
        onError: () => {
            console.log("Erro: ", installmentForm.errors);
        },
    });
};

onMounted(fetchEnums);
</script>

<template>
    <AppLayout title="Detalhes do Cartão de Crédito">
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Cartão: {{ props.card.nickname }} (Limite Disponível: R$
                    {{
                        parseFloat(props.card.available_limit)
                            .toFixed(2)
                            .replace(".", ",")
                    }})
                </h2>
            </div>
        </template>

        <VCard class="p-4 bg-white shadow rounded-lg">
            <VCardTitle>Compras</VCardTitle>
            <VCardText>
                <VBtn
                    @click="openCreatePurchaseModal"
                    class="mb-4 bg-primary px-2 hover:bg-blue-700 text-white"
                >
                    Nova Compra
                </VBtn>
                <VDataTable
                    :items="formattedPurchases"
                    :headers="[
                        {
                            title: 'Descrição',
                            key: 'description',
                            sortable: true,
                        },
                        { title: 'Valor (R$)', key: 'amount', sortable: true },
                        {
                            title: 'Data da Compra',
                            key: 'purchase_date',
                            sortable: true,
                        },
                        {
                            title: 'Categoria',
                            key: 'category',
                            sortable: false,
                        },
                    ]"
                    show-expand
                >
                    <template class="w-full" v-slot:expanded-row="{ item }">
                        <div class="mt-2 mb-3 flex justify-start">
                            <p class="font-semibold text-xl px-2">Parcelas</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <VCard
                                v-for="installment in item.installments"
                                :key="installment.id"
                            >
                                <VCardText>
                                    <h2 class="font-semibold text-md mt-2">
                                        Parcela -
                                        {{
                                            new Date(
                                                installment.due_date
                                            ).toLocaleDateString("pt-BR")
                                        }}
                                    </h2>
                                    <p
                                        class="text-lg"
                                        :color="
                                            installment.status === true
                                                ? 'green'
                                                : 'red'
                                        "
                                    >
                                        R$
                                        {{
                                            parseFloat(installment.amount)
                                                .toFixed(2)
                                                .replace(".", ",")
                                        }}
                                    </p>
                                    <v-btn
                                        v-if="installment.status === false"
                                        color="red"
                                        @click="
                                            payInstallment(installment)
                                        "
                                        class="mt-4"
                                        size="small"
                                    >
                                        <v-icon>mdi-credit-card-outline</v-icon
                                        >&nbsp;Pagar parcela
                                    </v-btn>
                                    <span v-if="installment.status === true">
                                        <v-icon>mdi-credit-card-check</v-icon
                                        >&nbsp;Parcela Paga
                                    </span>
                                </VCardText>
                            </VCard>
                        </div>
                    </template>
                </VDataTable>
            </VCardText>
        </VCard>

        <!-- Modal para criar nova compra -->
        <VDialog v-model="modalPurchaseVisible" persistent max-width="600">
            <VCard>
                <VCardTitle>{{
                    isEditingPurchase ? "Editar Compra" : "Nova Compra"
                }}</VCardTitle>
                <VCardText>
                    <VTextField
                        v-model="purchaseForm.description"
                        label="Descrição"
                        required
                    ></VTextField>
                    <VTextField
                        v-model="purchaseForm.amount"
                        label="Valor (R$)"
                        type="number"
                        required
                    ></VTextField>
                    <VTextField
                        v-model="purchaseForm.purchase_date"
                        label="Data da Compra"
                        type="date"
                        required
                    ></VTextField>
                    <VSelect
                        v-model="purchaseForm.category"
                        :items="formattedCategories"
                        item-text="title"
                        item-value="value"
                        label="Categoria"
                        required
                    ></VSelect>

                    <VTextField
                        v-model="purchaseForm.installments_count"
                        label="Número de Parcelas"
                        type="number"
                        min="1"
                        required
                    ></VTextField>
                </VCardText>
                <div class="flex justify-end p-4">
                    <VBtn @click="modalPurchaseVisible = false" class="mr-2"
                        >Cancelar</VBtn
                    >
                    <VBtn @click="submitPurchaseForm" color="primary">{{
                        isEditingPurchase ? "Salvar" : "Criar"
                    }}</VBtn>
                </div>
            </VCard>
        </VDialog>
    </AppLayout>
</template>

<style scoped>
.v-btn {
    font-size: 14px;
    padding: 4px;
}
</style>
