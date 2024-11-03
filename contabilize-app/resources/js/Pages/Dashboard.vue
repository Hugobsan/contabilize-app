<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MonthlyExpensesChart from '@/Components/Widgets/MonthlyExpensesChart.vue';
import MonthlyExpensesTable from '@/Components/Widgets/MonthlyExpensesTable.vue';
// import BalanceWidget from '@/Components/Widgets/BalanceWidget.vue';
// import CreditCardTransactionsTable from '@/Components/Widgets/CreditCardTransactionsTable.vue';
// import BalanceEvolutionChart from '@/Components/Widgets/BalanceEvolutionChart.vue';

const props = defineProps({
    reportData: Object,
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6 h-50">
                <MonthlyExpensesChart :data="reportData.monthlyExpenses" />
                <MonthlyExpensesTable :data="reportData.monthlyExpenses" />
            </div>

            
                <!-- <BalanceWidget :payableBalance="reportData.balance.totalPayable" :receivableBalance="reportData.balance.totalReceivable" /> -->
                <!-- <CreditCardTransactionsTable :transactions="reportData.transactions" /> -->
                <!-- <BalanceEvolutionChart :evolutionData="reportData.balanceEvolution" /> -->
        </div>
    </AppLayout>
</template>
