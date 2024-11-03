<?php

namespace App\Services;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\CreditCardPurchase;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Summary of getMonthlyExpensesByCategory
     * @param mixed $userId
     * @param mixed $year
     * @param mixed $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMonthlyExpensesByCategory($userId, $year, $month)
    {
        return AccountPayable::where('user_id', $userId)
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->select('category', DB::raw('SUM(value) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Summary of getTotalPayableBalance
     * @param mixed $userId
     * @return mixed
     */
    public function getTotalPayableBalance($userId)
    {
        return AccountPayable::where('user_id', $userId)
            ->where('status', 0)
            ->sum('value');
    }

    /**
     * Summary of getTotalReceivableBalance
     * @param mixed $userId
     * @return mixed
     */
    public function getTotalReceivableBalance($userId)
    {
        return AccountReceivable::where('user_id', $userId)
            ->where('status', 0) // Apenas as receitas não recebidas
            ->sum('value');
    }

    /**
     * Summary of getCreditCardTransactions
     * @param mixed $userId
     * @param mixed $year
     * @param mixed $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCreditCardTransactions($userId, $year = null, $month = null)
    {
        $query = CreditCardPurchase::whereHas('creditCard', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('creditCard');

        if ($year && $month) {
            $query->whereYear('purchase_date', $year)
                ->whereMonth('purchase_date', $month);
        }

        return $query->with('creditCard')->orderBy('purchase_date', 'desc')->get();
    }

    /**
     * Summary of getBalanceEvolution
     * @param mixed $userId
     * @param mixed $startDate
     * @param mixed $endDate
     * @return array{balance: float, date: mixed[]}
     */
    public function getBalanceEvolution($userId, $startDate, $endDate)
    {
        // Obter despesas e receitas
        $expenses = AccountPayable::where('user_id', $userId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->select(DB::raw('DATE(due_date) as date'), DB::raw('SUM(value) as total_expenses'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $receivables = AccountReceivable::where('user_id', $userId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->select(DB::raw('DATE(due_date) as date'), DB::raw('SUM(value) as total_receivables'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Formatar dados em um intervalo contínuo de datas
        $currentBalance = 0;
        $formattedData = [];
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dailyExpenses = $expenses->get($dateString)->total_expenses ?? 0;
            $dailyReceivables = $receivables->get($dateString)->total_receivables ?? 0;

            $currentBalance += $dailyReceivables - $dailyExpenses;
            $formattedData[] = [
                'date' => $date->format('c'), // Formato ISO 8601 para compatibilidade com o Chart.js
                'balance' => $currentBalance,
            ];
        }

        return $formattedData;
    }


    /**
     * Summary of getBalanceBetweenAccounts
     * @param mixed $userId
     * @return array
     */
    public function getBalanceBetweenAccounts($userId)
    {
        $totalPayable = $this->getTotalPayableBalance($userId);
        $totalReceivable = $this->getTotalReceivableBalance($userId);

        return [
            'totalPayable' => $totalPayable,
            'totalReceivable' => $totalReceivable,
            'balance' => $totalReceivable - $totalPayable,
        ];
    }

    public function getMonthlyReceivesByCategory($userId, $year, $month)
    {
        return AccountReceivable::where('user_id', $userId)
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->select('category', DB::raw('SUM(value) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
    }
}
