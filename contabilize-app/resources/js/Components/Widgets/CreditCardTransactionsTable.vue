<script setup>
import { ref } from 'vue';
import { VDataTable, VCard, VCardTitle, VCardText, VPagination } from 'vuetify/components';

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
});

const tableHeaders = [
    { title: 'Apelido do Cartão', align: 'start', sortable: true, key: 'cardNickname' },
    { title: 'Nome da Compra', align: 'start', sortable: true, key: 'description' },
    { title: 'Data da Compra', align: 'end', sortable: true, key: 'purchaseDate' },
    { title: 'Valor (R$)', align: 'end', sortable: true, key: 'amount' },
];

const itemsPerPage = ref(5);
const currentPage = ref(1);
</script>

<template>
    <VCard class="p-4 bg-white shadow rounded-lg" style="height: 400px; overflow-y: auto;">
        <VCardTitle class="text-center font-semibold">Transações de Cartão de Crédito</VCardTitle>
        <VCardText>
            <VDataTable
                :headers="tableHeaders"
                :items="props.data"
                class="elevation-1"
                item-value="id"
                :items-per-page="itemsPerPage"
                :page="currentPage"
                @update:page="(page) => currentPage = page"
                :footer-props="{
                    'items-per-page-options': [5, 10, 15],
                    showFirstLastPage: true,
                    'items-per-page-text': 'Itens por página'
                }"
            />
        </VCardText>
    </VCard>
</template>

<style scoped>
.VCard {
    width: 100%;
}
</style>
