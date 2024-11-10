<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import axios from "axios";
import {
    VCard,
    VCardTitle,
    VCardText,
    VBtn,
    VDialog,
    VTextField,
    VSelect,
    VDataTable,
} from "vuetify/components";
import AppLayout from "@/Layouts/AppLayout.vue";
import "@mdi/font/css/materialdesignicons.css";

const props = defineProps({ accounts: Array });

const accountsReceivable = ref(props.accounts);
const modalVisible = ref(false);
const isEditing = ref(false);
const selectedAccount = ref(null);

const form = useForm({
    description: "",
    value: "",
    due_date: "",
    status: "",
    category: "",
    recurrence_period: "",
});

const categories = ref([]);
const statuses = ref([]);
const recurrencePeriods = ref([]);

// Função para buscar enums de categorias, status e períodos de recorrência
const fetchEnums = async () => {
    try {
        const categoryResponse = await axios.get("/enums/receivable-category");
        categories.value = categoryResponse.data.map((item) => ({
            value: item.value,
            label: item.label,
        }));

        const statusResponse = await axios.get("/enums/status");
        statuses.value = statusResponse.data.map((item) => ({
            value: item.value,
            label: item.label,
        }));

        const recurrenceResponse = await axios.get("/enums/recurrence-period");
        recurrencePeriods.value = recurrenceResponse.data.map((item) => ({
            value: item.value,
            label: item.label,
        }));
    } catch (error) {
        console.error("Erro ao buscar enums:", error);
    }
};

// Computed para formatar contas a receber e exibir labels corretos
const formattedAccountsReceivable = computed(() => {
    return accountsReceivable.value.map((account) => ({
        ...account,
        status:
            statuses.value.find((status) => status.value === account.status)
                ?.label || account.status,
        category:
            categories.value.find(
                (category) => category.value === account.category
            )?.label || account.category,
        value: `R$ ${parseFloat(account.value).toFixed(2).replace(".", ",")}`,
        due_date: new Date(account.due_date).toLocaleDateString("pt-BR"),
    }));
});

// Funções de controle de modal e formulário
const openCreateModal = () => {
    resetForm();
    isEditing.value = false;
    modalVisible.value = true;
};

const openEditModal = (account) => {
    form.description = account.description;
    let value = account.value
        .replace("R$ ", "")
        .replace(",", ".")
        .replace(/[^\d.]/g, "");
    form.value = parseFloat(value).toFixed(2);
    form.due_date = account.due_date.split("/").reverse().join("-");

    let formattedStatus = statuses.value.find(
        (status) => status.label === account.status
    );
    form.status = formattedStatus.value;

    let formattedCategory = categories.value.find(
        (category) => category.label === account.category
    );
    form.category = formattedCategory.value;

    if (account.recurrence_period) {
        console.log(recurrencePeriods);
        let formattedRecurrencePeriod = recurrencePeriods.value.find(
            (recurrencePeriod) => recurrencePeriod.label === account.recurrence_period
        );
        console.log(account.recurrence_period, formattedRecurrencePeriod);
        form.recurrence_period = formattedRecurrencePeriod.value;
    }
    else{
        form.recurrence_period = "";
    }
    isEditing.value = true;
    selectedAccount.value = account;
    modalVisible.value = true;
};

const resetForm = () => {
    form.description = "";
    form.value = "";
    form.due_date = "";
    form.status = "";
    form.category = "";
    form.recurrence_period = "";
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(
            route("accounts-receivable.update", selectedAccount.value.id),
            {
                data: form,
                onSuccess: () => {
                    modalVisible.value = false;
                    resetForm();
                },
                onError: (error) => {
                    alert(
                        "Erro ao atualizar conta. Verifique os campos e tente novamente."
                    );
                    console.error("Erro ao atualizar conta:", error);
                },
            }
        );
    } else {
        form.post(route("accounts-receivable.store"), {
            onSuccess: () => {
                modalVisible.value = false;
                resetForm();
            },
            onError: (error) => {
                alert(
                    "Erro ao criar conta. Verifique os campos e tente novamente."
                );
                console.error("Erro ao criar conta:", error);
            },
        });
    }
};

const deleteAccount = (accountId) => {
    if (confirm("Tem certeza que deseja excluir esta conta?")) {
        form.delete(route("accounts-receivable.destroy", accountId), {
            onSuccess: () => {
                accountsReceivable.value = accountsReceivable.value.filter(
                    (acc) => acc.id !== accountId
                );
            },
        });
    }
};

const formattedCategories = computed(() => {
    return categories.value.map((item) => ({
        value: item.value,
        title: item.label,
    }));
});

const formattedStatuses = computed(() => {
    return statuses.value.map((item) => ({
        value: item.value,
        title: item.label,
    }));
});

const formattedRecurrencePeriods = computed(() => {
    return recurrencePeriods.value.map((item) => ({
        value: item.value,
        title: item.label,
    }));
});

onMounted(fetchEnums);
</script>

<template>
    <AppLayout title="Contas a Receber">
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Contas a Receber
                </h2>
            </div>
        </template>

        <VCard class="p-4 bg-white shadow rounded-lg">
            <VCardText>
                <VBtn
                    @click="openCreateModal"
                    class="mb-4 bg-primary text-white"
                    >Nova Conta</VBtn
                >
                <VDataTable
                    :items="formattedAccountsReceivable"
                    :headers="[
                        {
                            title: 'Descrição',
                            key: 'description',
                            sortable: true,
                        },
                        { title: 'Valor (R$)', key: 'value', sortable: true },
                        {
                            title: 'Data de Vencimento',
                            key: 'due_date',
                            sortable: true,
                        },
                        { title: 'Status', key: 'status', sortable: true },
                        { title: 'Categoria', key: 'category', sortable: true },
                        { title: 'Opções', key: 'actions', sortable: false },
                    ]"
                >
                    <template v-slot:item.actions="{ item }">
                        <VBtn
                            small
                            @click="openEditModal(item)"
                            class="mr-2"
                            title="Editar"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </VBtn>
                        <VBtn
                            small
                            color="error"
                            @click="deleteAccount(item.id)"
                            title="Excluir"
                        >
                            <v-icon>mdi-delete</v-icon>
                        </VBtn>
                    </template>
                </VDataTable>
            </VCardText>
        </VCard>

        <VDialog v-model="modalVisible" persistent max-width="600">
            <VCard>
                <VCardTitle>{{
                    isEditing ? "Editar Conta" : "Nova Conta"
                }}</VCardTitle>
                <VCardText>
                    <VTextField
                        v-model="form.description"
                        label="Descrição"
                        required
                    ></VTextField>
                    <VTextField
                        v-model="form.value"
                        label="Valor (R$)"
                        type="number"
                        required
                    ></VTextField>
                    <VTextField
                        v-model="form.due_date"
                        label="Data de Vencimento"
                        type="date"
                        required
                    ></VTextField>
                    <VSelect
                        v-model="form.status"
                        :items="formattedStatuses"
                        item-text="title"
                        item-value="value"
                        label="Status"
                        required
                    ></VSelect>

                    <VSelect
                        v-model="form.category"
                        :items="formattedCategories"
                        item-text="title"
                        item-value="value"
                        label="Categoria"
                        required
                    ></VSelect>

                    <VSelect
                        v-model="form.recurrence_period"
                        :items="formattedRecurrencePeriods"
                        item-text="title"
                        item-value="value"
                        label="Período de Recorrência"
                    ></VSelect>
                </VCardText>
                <div class="flex justify-end p-4">
                    <VBtn @click="modalVisible = false" class="mr-2"
                        >Cancelar</VBtn
                    >
                    <VBtn @click="submitForm" color="primary">{{
                        isEditing ? "Salvar" : "Criar"
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
