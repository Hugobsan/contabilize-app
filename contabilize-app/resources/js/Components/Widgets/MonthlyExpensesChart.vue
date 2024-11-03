<script setup>
import Chart from 'chart.js/auto';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    data: Array,
});

const chartRef = ref(null);
const categoryMap = ref({});

// Função para buscar as categorias e criar um mapeamento de valor para label
const fetchCategoryLabels = async () => {
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

onMounted(async () => {
    await fetchCategoryLabels();

    if (chartRef.value) {
        new Chart(chartRef.value, {
            type: 'bar',
            data: {
                labels: props.data.map(item => categoryMap.value[item.category] || item.category),
                datasets: [
                    {
                        label: 'Despesas Mensais por Categoria',
                        data: props.data.map(item => parseFloat(item.total)),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    }
});
</script>

<template>
    <div class="p-4 bg-white shadow rounded-lg">
        <h3 class="text-center font-semibold mb-4">Despesas Mensais por Categoria</h3>
        <canvas ref="chartRef"></canvas>
    </div>
</template>

<style scoped>
canvas {
    width: 100%;
    max-width: 600px;
}
</style>
