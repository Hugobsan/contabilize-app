<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { Head, Link, useForm } from '@inertiajs/vue3';
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

// Props recebidas do backend
const props = defineProps({ accounts: Array });

const accountsReceivable = ref(props.accounts);
const modalVisible = ref(false);
const isEditing = ref(false);
const selectedAccount = ref(null);

const updateForm = useForm({
    description: "",
    value: "",
    due_date: "",
    status: "",
    category: "",
    recurrence_period: "",
    _method: "put",
    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
});

const form = reactive({
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

        console.log("Categories:", categories.value);
        console.log("Statuses:", statuses.value);
        console.log("Recurrence Periods:", recurrencePeriods.value);
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
    form.value = account.value;
    form.due_date = account.due_date;
    form.status = account.status;
    form.category = account.category;
    form.recurrence_period = account.recurrence_period;
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
        updateForm.put(route("accounts-receivable.update", selectedAccount.value.id), {
            data: form,
            onSuccess: () => {
                modalVisible.value = false;
                resetForm();
            },
        });
    } else {
        updateForm.post(route("accounts-receivable.store"), {
            data: form,
            onSuccess: () => {
                modalVisible.value = false;
                resetForm();
            },
        });
    }
};

const deleteAccount = (accountId) => {
    if (confirm('Tem certeza que deseja excluir esta conta?')) {
        updateForm.delete(route("accounts-receivable.destroy", accountId), {
            onSuccess: () => {
                accountsReceivable.value = accountsReceivable.value.filter(acc => acc.id !== accountId);
            }
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
                    class="mb-4 bg-blue-500 hover:bg-blue-700 text-white"
                    >Nova Conta</VBtn
                >
                <VDataTable
                    :items="formattedAccountsReceivable"
                    :headers="[{
                        title: 'Descrição',
                        key: 'description',
                        sortable: true,
                    },
                    { title: 'Valor (R$)', key: 'value', sortable: true },
                    { title: 'Data de Vencimento', key: 'due_date', sortable: true },
                    { title: 'Status', key: 'status', sortable: true },
                    { title: 'Categoria', key: 'category', sortable: true },
                    { title: 'Opções', key: 'actions', sortable: false }]"
                >
                    <template v-slot:item.actions="{ item }">
                        <VBtn
                            small
                            @click="openEditModal(item)"
                            class="mr-2"
                            title="Editar"
                            >✏️</VBtn
                        >
                        <VBtn
                            small
                            color="error"
                            @click="deleteAccount(item.id)"
                            title="Excluir"
                            >🗑️</VBtn
                        >
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
