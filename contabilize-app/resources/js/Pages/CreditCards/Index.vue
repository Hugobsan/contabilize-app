<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { Head, Link, useForm } from '@inertiajs/vue3';
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
const props = defineProps({ creditCards: Array });

const creditCards = ref(props.creditCards);
const modalVisible = ref(false);
const isEditing = ref(false);
const selectedCard = ref(null);

const updateForm = useForm({
    nickname: "",
    credit_limit: "",
    available_limit: "",
    _method: "put",
    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
});

const form = reactive({
    nickname: "",
    credit_limit: "",
    available_limit: "",
});

// Computed para formatar o limite de crédito
const formattedCreditCards = computed(() => {
    return creditCards.value.map((card) => ({
        ...card,
        credit_limit: `R$ ${parseFloat(card.credit_limit).toFixed(2).replace(".", ",")}`,
        available_limit: `R$ ${parseFloat(card.available_limit).toFixed(2).replace(".", ",")}`,
    }));
});

// Funções de controle de modal e formulário
const openCreateModal = () => {
    resetForm();
    isEditing.value = false;
    modalVisible.value = true;
};

const openEditModal = (card) => {
    form.nickname = card.nickname;
    form.credit_limit = parseFloat(card.credit_limit.replace('R$', '').replace(',', '.'));
    form.available_limit = parseFloat(card.available_limit.replace('R$', '').replace(',', '.'));
    isEditing.value = true;
    selectedCard.value = card;
    modalVisible.value = true;
};

const resetForm = () => {
    form.nickname = "";
    form.credit_limit = "";
    form.available_limit = "";
};

const submitForm = () => {
    if (isEditing.value) {
        updateForm.put(route("credit-cards.update", selectedCard.value.id), {
            data: form,
            onSuccess: () => {
                modalVisible.value = false;
                resetForm();
            },
        });
    } else {
        updateForm.post(route("credit-cards.store"), {
            data: form,
            onSuccess: () => {
                modalVisible.value = false;
                resetForm();
            },
        });
    }
};

const deleteCard = (cardId) => {
    if (confirm('Tem certeza que deseja excluir este cartão?')) {
        updateForm.delete(route("credit-cards.destroy", cardId), {
            onSuccess: () => {
                creditCards.value = creditCards.value.filter(card => card.id !== cardId);
            }
        });
    }
};

</script>

<template>
    <AppLayout title="Cartões de Crédito">
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Cartões de Crédito
                </h2>
            </div>
        </template>

        <VCard class="p-4 bg-white shadow rounded-lg">
            <VCardText>
                <VBtn
                    @click="openCreateModal"
                    class="mb-4 bg-blue-500 hover:bg-blue-700 text-white"
                    >Novo Cartão</VBtn
                >
                <VDataTable
                    :items="formattedCreditCards"
                    :headers="[{
                        title: 'Apelido',
                        key: 'nickname',
                        sortable: true,
                    },
                    { title: 'Limite de Crédito (R$)', key: 'credit_limit', sortable: true },
                    { title: 'Limite Disponível (R$)', key: 'available_limit', sortable: true },
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
                            @click="deleteCard(item.id)"
                            title="Excluir"
                            >🗑️</VBtn
                        >
                        <VBtn
                            small
                            color="info"
                            :href="route('credit-cards.show', item.id)"
                            title="Visualizar"
                            >📄</VBtn
                        >
                    </template>
                </VDataTable>
            </VCardText>
        </VCard>

        <VDialog v-model="modalVisible" persistent max-width="600">
            <VCard>
                <VCardTitle>{{
                    isEditing ? "Editar Cartão" : "Novo Cartão"
                }}</VCardTitle>
                <VCardText>
                    <VTextField
                        v-model="form.nickname"
                        label="Apelido"
                        required
                    ></VTextField>
                    <VTextField
                        v-model="form.credit_limit"
                        label="Limite de Crédito (R$)"
                        type="number"
                        min="0"
                        required
                    ></VTextField>
                    <VTextField
                        v-model="form.available_limit"
                        label="Limite Disponível (R$)"
                        type="number"
                        min="0"
                        :max="form.credit_limit"
                        required
                    ></VTextField>
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
