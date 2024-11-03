<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { VDataTable, VCard, VCardTitle, VCardText, VPagination } from 'vuetify/components';

const props = defineProps({
    data: Array,
});

const categoryMap = ref({});

// Função para buscar as categorias e criar um mapeamento de valor para label
const fetchCategories = async () => {
    try {
        const response = await axios.get('/categories');
        categoryMap.value = response.data.reduce((map, category) => {
            map[category.value] = category.label;
            return map;
        }, {});
    } catch (error) {
        console.error('Erro ao buscar categorias:', error);
    }
};

// Função para obter o rótulo da categoria
const getCategoryLabel = (category) => {
    return categoryMap.value[category] || category;
};

// Computed para transformar os dados antes de passar para a tabela
const formattedData = computed(() => {
    return props.data.map(item => ({
        ...item,
        category: getCategoryLabel(item.category),
    }));
});

const tableHeaders = [
    { title: 'Categoria', align: 'start', sortable: true, key: 'category' },
    { title: 'Total (R$)', align: 'end', sortable: true, key: 'total' },
];

const formatCurrency = (value) => {
    return `R$ ${parseFloat(value).toFixed(2).replace('.', ',')}`;
};

const itemsPerPage = ref(5);
const currentPage = ref(1);

onMounted(async () => {
    await fetchCategories();
});
</script>

<template>
    <VCard class="p-4 bg-white shadow rounded-lg" style="height: 400px; overflow-y: auto;">
        <VCardTitle class="text-center font-semibold">Despesas Mensais por Categoria</VCardTitle>
        <VCardText>
            <VDataTable
                :headers="tableHeaders"
                :items="formattedData"
                class="elevation-1"
                :items-per-page="itemsPerPage"
                :page="currentPage"
                @update:page="(page) => currentPage.value = page"
                :footer-props="{
                    'items-per-page-options': [5, 10, 15],
                    showFirstLastPage: true,
                    'items-per-page-text': 'Itens por página'
                }"
            >
                <template v-slot:body.cell.total="{ item }">
                    <span>{{ formatCurrency(item.total) }}</span>
                </template>
            </VDataTable>
            <VPagination
                v-model="currentPage"
                :length="Math.ceil(props.data.length / itemsPerPage)"
                class="mt-4"
                style="color: #000;"
            />
        </VCardText>
    </VCard>
</template>

<style scoped>
.VCard {
    width: 100%;
}
.VPagination {
    background-color: #f0f0f0;
    color: #000;
}
</style>
