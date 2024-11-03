<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInstallment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseInstallmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PurchaseInstallment::class, 'purchaseInstallment');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseInstallment $purchaseInstallment): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('PurchaseInstallments/Show', ['installment' => $purchaseInstallment]);

        return response()->json($purchaseInstallment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseInstallment $purchaseInstallment): JsonResponse
    {
        DB::beginTransaction();
        try {
            $purchaseInstallment->delete();
            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('purchase-installments.index')->with('success', 'Parcela excluída com sucesso!');

            return response()->json(['message' => 'Parcela excluída com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir a parcela: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao excluir a parcela. Tente novamente mais tarde.'], 500);
        }
    }
}
