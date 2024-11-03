<script setup>
import Chart from 'chart.js/auto';
import { ref, onMounted } from 'vue';
import 'chartjs-adapter-date-fns'; // Certifique-se de que esse adaptador esteja instalado

const props = defineProps({
    title: String,
    data: Array, // Dados no formato [{ date: '2024-11-04T00:00:00Z', balance: 1000 }, ...]
});

const chartRef = ref(null);

onMounted(() => {
    if (chartRef.value) {
        new Chart(chartRef.value, {
            type: 'line',
            data: {
                labels: props.data.map(item => new Date(item.date)), // Converte strings de data em objetos Date
                datasets: [
                    {
                        label: props.title,
                        data: props.data.map(item => item.balance),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.1, // Suaviza a linha
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'time', // Define o eixo X como uma escala de tempo
                        time: {
                            unit: 'day', // Mostra as datas em dias
                        },
                    },
                    y: {
                        beginAtZero: false,
                    },
                },
            },
        });
    }
});
</script>

<template>
    <div class="p-4 bg-white shadow rounded-lg">
        <h3 class="text-center font-semibold mb-4">{{ props.title }}</h3>
        <canvas ref="chartRef"></canvas>
    </div>
</template>

<style scoped>
canvas {
    width: 100%;
    max-width: 800px;
}
</style>
