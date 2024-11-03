<script setup>
import { VCard, VCardTitle, VCardText } from 'vuetify/components';

const props = defineProps({
    title: String,
    value: Number,
});

const formatCurrency = (value) => {
    return `R$ ${parseFloat(value).toFixed(2).replace('.', ',')}`;
};

// Computed class for color based on the value
const getColorClass = (value) => {
    if (value > 0) {
        return 'text-green-500'; // Verde (positivo)
    } else if (value < 0) {
        return 'text-red-500'; // Vermelho (negativo)
    } else {
        return 'text-black'; // Preto (zero)
    }
};
</script>

<template>
    <VCard class="p-4 bg-white shadow rounded-lg text-center" style="height: 150px;">
        <VCardTitle class="text-center font-semibold">{{ title }}</VCardTitle>
        <VCardText :class="getColorClass(value)" class="text-2xl font-bold">
            {{ formatCurrency(value) }}
        </VCardText>
    </VCard>
</template>

<style scoped>
.VCard {
    width: 100%;
}
</style>
