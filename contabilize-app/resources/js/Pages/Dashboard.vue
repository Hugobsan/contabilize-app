<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {Link} from '@inertiajs/vue3';
import MonthlyExpensesChart from '@/Components/Widgets/MonthlyExpensesChart.vue';
import MonthlyExpensesTable from '@/Components/Widgets/MonthlyExpensesTable.vue';
import BalanceWidget from '@/Components/Widgets/BalanceWidget.vue';
import TimeSeriesChart from '@/Components/Widgets/TimeSeriesChart.vue';
import CreditCardTransactionsTable from '@/Components/Widgets/CreditCardTransactionsTable.vue';

const props = defineProps({
    reportData: Object,
});

// Função para formatar os dados das transações
const formatTransactionsData = (transactions) => {

    const newTrans = transactions.map(transaction => ({
        cardNickname: transaction.credit_card.nickname,
        description: transaction.description,
        purchaseDate: new Date(transaction.purchase_date).toLocaleDateString('pt-BR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
        }),
        amount: parseFloat(transaction.amount).toFixed(2).replace('.', ','),
    }));

    return newTrans;
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex flex-row justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <!-- <Link :href="route('dashboard.pdf')"> -->
                <a :href="route('dashboard.pdf')">
                <div class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded hover:cursor-pointer" @submit.prevent="logout">
                    Exportar Relatório
                </div>
                </a>
            <!-- </Link> -->
        </div>
        </template>
        <div class="py-2">
            <div class="max-w-7xl mx-auto my-5 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <BalanceWidget title="Saldo Total" :value="reportData.balance.totalReceivable - reportData.balance.totalPayable" />
                <BalanceWidget title="Receitas Totais" :value="Number(reportData.balance.totalReceivable)" />
                <BalanceWidget title="Despesas Totais" :value="reportData.balance.totalPayable * -1" />
            </div>

            <div class="max-w-7xl mx-auto my-5 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6 h-50">
                <MonthlyExpensesChart :data="reportData.monthlyExpenses" />
                <MonthlyExpensesTable :data="reportData.monthlyExpenses" />
            </div>

            <div class="max-w-7xl mx-auto my-5 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6 h-50">
                <TimeSeriesChart :title="'Evolução do Saldo ao Longo do Tempo'" :data="reportData.balanceEvolution" />
                <CreditCardTransactionsTable :transactions="formatTransactionsData(reportData.transactions)" />
            </div>
        </div>

    </AppLayout>
</template>
