<script setup>
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
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

const accountsPayable = ref(props.accounts);
const modalVisible = ref(false);
const isEditing = ref(false);
const selectedAccount = ref(null);

const form = useForm({
    description: "",
    value: "",
    due_date: "",
    status: "",
    category: "",
});

const categories = ref([]);
const statuses = ref([]);

// Função para buscar enums de categorias e status
const fetchEnums = async () => {
    try {
        const categoryResponse = await axios.get("/enums/category");
        categories.value = categoryResponse.data.map((item) => ({
            value: item.value,
            label: item.label,
        }));

        const statusResponse = await axios.get("/enums/status");
        statuses.value = statusResponse.data.map((item) => ({
            value: item.value,
            label: item.label,
        }));
    } catch (error) {
        console.error("Erro ao buscar enums:", error);
    }
};

// Computed para formatar contas a pagar e exibir labels corretos
const formattedAccountsPayable = computed(() => {
    return accountsPayable.value.map((account) => ({
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

    // Corrige o formato do valor para número
    let value = account.value
        .replace("R$ ", "")
        .replace(",", ".")
        .replace(/[^\d.]/g, "");
    form.value = parseFloat(value).toFixed(2);

    // Corrige o formato da data para "AAAA-MM-DD"
    form.due_date = account.due_date.split("/").reverse().join("-");

    let formattedStatus = statuses.value.find(
        (status) => status.label === account.status
    );
    form.status = formattedStatus.value;

    let formattedCategory = categories.value.find(
        (category) => category.label === account.category
    );
    form.category = formattedCategory.value;
    isEditing.value = account;
    selectedAccount.value = account;
    modalVisible.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route("accounts-payable.update", selectedAccount.value.id), {
            data: form,
            onSuccess: () => {
                const index = accountsPayable.value.findIndex(
                    (acc) => acc.id === isEditing.value.id
                );
                if (index !== -1) {
                    // Atualize o item diretamente na tabela com os novos valores
                    accountsPayable.value[index] = {
                        ...accountsPayable.value[index],
                        description: form.description,
                        value: `R$ ${parseFloat(form.value)
                            .toFixed(2)
                            .replace(".", ",")}`, // Formate o valor
                        due_date: new Date(form.due_date).toLocaleDateString(
                            "pt-BR"
                        ), // Formate a data
                        status:
                            statuses.value.find(
                                (status) => status.value === form.status
                            )?.label || form.status,
                        category:
                            categories.value.find(
                                (category) => category.value === form.category
                            )?.label || form.category,
                    };
                }

                modalVisible.value = false;
                resetForm();
            },
            onError: (response) => {
                console.error("Erro ao editar conta a pagar:", response);
            },
        });
    } else {
        form.post(route("accounts-payable.store"), {
            onSuccess: () => {
                modalVisible.value = false;
                resetForm();
            },
            onError: (response) => {
                console.error("Erro ao criar conta a pagar:", response);
            },
        });
    }
};

const resetForm = () => {
    form.description = "";
    form.value = "";
    form.due_date = "";
    form.status = "";
    form.category = "";
};

const deleteAccount = (accountId) => {
    if (confirm("Tem certeza que deseja excluir esta conta?")) {
        form.delete(route("accounts-payable.destroy", accountId), {
            data: { _method: "delete", id: accountId },
            onSuccess: () => {
                accountsPayable.value = accountsPayable.value.filter(
                    (acc) => acc.id !== accountId
                );
            },
        });
    }
};

const markAsPaid = (account) => {
    const patchForm = useForm({
        status: 1,
    });
    patchForm.put(route("accounts-payable.update", account.id), {
        onSuccess: () => {
            const index = accountsPayable.value.findIndex(
                (acc) => acc.id === account.id
            );
            if (index !== -1) {
                accountsPayable.value[index].status = "Paga";
            }
        },
        onError: (response) => {
            alert("Erro ao marcar conta como paga");
            console.error("Erro ao marcar conta como paga:", response);
        },
    });
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

onMounted(fetchEnums);
</script>

<template>
    <AppLayout title="Contas a Pagar">
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Contas a Pagar
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
                    :items="formattedAccountsPayable"
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
                            <VIcon>mdi-pencil</VIcon>
                        </VBtn>
                        <VBtn
                            small
                            color="error"
                            @click="deleteAccount(item.id)"
                            title="Excluir"
                        >
                            <VIcon>mdi-delete</VIcon>
                        </VBtn>
                        <VBtn
                            v-if="item.status === 'Pendente'"
                            small
                            color="success"
                            @click="markAsPaid(item)"
                            title="Marcar como Pago"
                        >
                            <VIcon>mdi-cash</VIcon>
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
