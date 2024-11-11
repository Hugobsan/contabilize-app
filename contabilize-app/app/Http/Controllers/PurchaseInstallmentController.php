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
    protected function authorizeMe($permission, $accountPayable = PurchaseInstallment::class)
    {
        if (!auth()->user()->can($permission, $accountPayable)) {
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }
    }

    public function update(Request $request, PurchaseInstallment $purchaseInstallment)
    {
        $this->authorizeMe('update', $purchaseInstallment);

        $request->validate([
            'due_date' => 'nullable|date',
            'value' => 'nullable|numeric',
            'status' => 'nullable|in:0,1',
            'purchase_id' => 'nullable|exists:credit_card_purchases,id',
        ]);

        DB::beginTransaction();
        try {
            $purchaseInstallment->update($request->all());
            DB::commit();

            return back()->with('success', 'Parcela atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar a parcela: ' . $e->getMessage());

            return back()->with('error', 'Erro ao atualizar a parcela. Tente novamente mais tarde.');
        }
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

            return response()->json(['message' => 'Parcela excluída com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir a parcela: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao excluir a parcela. Tente novamente mais tarde.'], 500);
        }
    }
}
