<?php

namespace App\Http\Controllers;

use App\Services\FinancialReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class ReportController extends Controller
{
    protected $financialReportService;

    public function __construct(FinancialReportService $financialReportService)
    {
        $this->financialReportService = $financialReportService;
    }

    /**
     * Exibe os dados do relatório em JSON.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $reportData = [
            'monthlyExpenses' => $this->financialReportService->getMonthlyExpensesByCategory($userId, $year, $month),
            'balance' => $this->financialReportService->getBalanceBetweenAccounts($userId),
            'transactions' => $this->financialReportService->getCreditCardTransactions($userId, $year, $month),
            'balanceEvolution' => $this->financialReportService->getBalanceEvolution($userId, $startDate, $endDate),
        ];

        //dd("Aqui"); //Chegou até aqui
        return Inertia::render('Dashboard', [
            'reportData' => $reportData,
        ]);

        // return response()->json($reportData);
    }

    /**
     * Gera o PDF do relatório financeiro.
     */
    public function downloadPdf(Request $request)
    {
        $userId = Auth::id();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $reportData = [
            'monthlyExpenses' => $this->financialReportService->getMonthlyExpensesByCategory($userId, $year, $month),
            'balance' => $this->financialReportService->getBalanceBetweenAccounts($userId),
            'transactions' => $this->financialReportService->getCreditCardTransactions($userId, $year, $month),
            'balanceEvolution' => $this->financialReportService->getBalanceEvolution($userId, $startDate, $endDate),
        ];

        $pdf = Pdf::loadView('reports.financial_report', $reportData);

        return $pdf->download('relatorio-financeiro.pdf');
    }
}
