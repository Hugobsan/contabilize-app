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

const purchaseForm = useForm({
    description: "",
    value: "",
    purchase_date: "",
    due_date: "",
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

// Abrir modal para nova parcela
const openCreateInstallmentModal = (purchase) => {
    installmentForm.purchase_id = purchase.id;
    modalInstallmentVisible.value = true;
};

// Resetar formulário de compra
const resetPurchaseForm = () => {
    purchaseForm.description = "";
    purchaseForm.value = "";
    purchaseForm.purchase_date = "";
    purchaseForm.due_date = "";
};

// Submeter formulário de compra
const submitPurchaseForm = () => {
    if (isEditingPurchase.value) {
        purchaseForm.put(route("purchases.update", selectedPurchase.id), {
            onSuccess: () => {
                modalPurchaseVisible.value = false;
                resetPurchaseForm();
            },
        });
    } else {
        purchaseForm.post(route("purchases.store"), {
            onSuccess: () => {
                modalPurchaseVisible.value = false;
                resetPurchaseForm();
                // Atualizar a lista de compras com a nova compra
            },
        });
    }
};

// Submeter formulário de parcela
const submitInstallmentForm = () => {
    installmentForm.post(route("installments.store"), {
        onSuccess: () => {
            modalInstallmentVisible.value = false;
            // Atualizar a lista de parcelas associadas à compra
        },
    });
};
</script>

<template>
    <AppLayout title="Detalhes do Cartão de Crédito">
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Cartão: {{ props.card.nickname }} (Limite Disponível: R$ {{ parseFloat(props.card.available_limit).toFixed(2).replace('.', ',') }})
                </h2>
            </div>
        </template>

        <VCard class="p-4 bg-white shadow rounded-lg">
            <VCardTitle>Compras</VCardTitle>
            <VCardText>
                <VBtn @click="openCreatePurchaseModal" class="mb-4 bg-blue-500 hover:bg-blue-700 text-white">
                    Nova Compra
                </VBtn>
                <VDataTable
                    :items="purchases"
                    :headers="[{
                        title: 'Descrição', key: 'description', sortable: true },
                        { title: 'Valor (R$)', key: 'value', sortable: true },
                        { title: 'Data da Compra', key: 'purchase_date', sortable: true },
                        { title: 'Data de Vencimento', key: 'due_date', sortable: true },
                        { title: 'Parcelas', key: 'installments', sortable: false },
                        { title: 'Opções', key: 'actions', sortable: false },
                    ]"
                >
                    <template v-slot:item.installments="{ item }">
                        <ul>
                            <li v-for="installment in item.installments" :key="installment.id">
                                Parcela de R$ {{ parseFloat(installment.amount).toFixed(2).replace('.', ',') }} - {{ new Date(installment.due_date).toLocaleDateString("pt-BR") }}
                            </li>
                        </ul>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <VBtn small @click="openCreateInstallmentModal(item)" title="Adicionar Parcela">➕</VBtn>
                    </template>
                </VDataTable>
            </VCardText>
        </VCard>

        <!-- Modal para criar nova compra -->
        <VDialog v-model="modalPurchaseVisible" persistent max-width="600">
            <VCard>
                <VCardTitle>{{ isEditingPurchase ? "Editar Compra" : "Nova Compra" }}</VCardTitle>
                <VCardText>
                    <VTextField v-model="purchaseForm.description" label="Descrição" required></VTextField>
                    <VTextField v-model="purchaseForm.value" label="Valor (R$)" type="number" required></VTextField>
                    <VTextField v-model="purchaseForm.purchase_date" label="Data da Compra" type="date" required></VTextField>
                    <VTextField v-model="purchaseForm.due_date" label="Data de Vencimento" type="date" required></VTextField>
                </VCardText>
                <div class="flex justify-end p-4">
                    <VBtn @click="modalPurchaseVisible = false" class="mr-2">Cancelar</VBtn>
                    <VBtn @click="submitPurchaseForm" color="primary">{{ isEditingPurchase ? "Salvar" : "Criar" }}</VBtn>
                </div>
            </VCard>
        </VDialog>

        <!-- Modal para criar nova parcela -->
        <VDialog v-model="modalInstallmentVisible" persistent max-width="600">
            <VCard>
                <VCardTitle>Nova Parcela</VCardTitle>
                <VCardText>
                    <VTextField v-model="installmentForm.amount" label="Valor da Parcela (R$)" type="number" required></VTextField>
                    <VTextField v-model="installmentForm.due_date" label="Data de Vencimento" type="date" required></VTextField>
                </VCardText>
                <div class="flex justify-end p-4">
                    <VBtn @click="modalInstallmentVisible = false" class="mr-2">Cancelar</VBtn>
                    <VBtn @click="submitInstallmentForm" color="primary">Criar</VBtn>
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
