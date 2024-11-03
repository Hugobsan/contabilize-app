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
        });

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
        $expenses = AccountPayable::where('user_id', $userId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->select(DB::raw('DATE(due_date) as date'), DB::raw('SUM(value) as total_expenses'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $receivables = AccountReceivable::where('user_id', $userId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->select(DB::raw('DATE(due_date) as date'), DB::raw('SUM(value) as total_receivables'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Combinar despesas e receitas em um formato unificado
        $combinedData = [];
        foreach ($expenses as $expense) {
            $combinedData[$expense->date]['total_expenses'] = $expense->total_expenses;
        }
        foreach ($receivables as $receivable) {
            $combinedData[$receivable->date]['total_receivables'] = $receivable->total_receivables;
        }

        // Calcular o saldo para cada data
        $formattedData = [];
        $currentBalance = 0;
        foreach ($combinedData as $date => $data) {
            $currentBalance += ($data['total_receivables'] ?? 0) - ($data['total_expenses'] ?? 0);
            $formattedData[] = [
                'date' => $date,
                'balance' => $currentBalance, // Saldo resultante de receitas - despesas
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
}
