<?php

namespace App\Http\Controllers;

use App\Models\CreditCardPurchase;
use App\Http\Requests\StoreCreditCardPurchaseRequest;
use App\Http\Requests\UpdateCreditCardPurchaseRequest;
use App\Models\CreditCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditCardPurchaseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CreditCardPurchase::class, 'creditCardPurchase');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $purchases = CreditCardPurchase::with('creditCard')
            ->whereHas('creditCard', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

        // Comentário para uso com Inertia:
        // return Inertia::render('CreditCardPurchases/Index', ['purchases' => $purchases]);

        return response()->json($purchases);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('CreditCardPurchases/Create');

        return response()->json(['message' => 'Formulário de criação de compra com cartão de crédito']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreditCardPurchaseRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $purchase = CreditCardPurchase::create($request->validated());

            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('credit-card-purchases.index')->with('success', 'Compra criada com sucesso!');

            return response()->json(['message' => 'Compra criada com sucesso!', 'purchase' => $purchase]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar a compra: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao criar a compra. Tente novamente mais tarde.'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(CreditCardPurchase $creditCardPurchase): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('CreditCardPurchases/Show', ['purchase' => $creditCardPurchase]);

        return response()->json($creditCardPurchase);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreditCardPurchase $creditCardPurchase): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('CreditCardPurchases/Edit', ['purchase' => $creditCardPurchase]);

        return response()->json(['message' => 'Formulário de edição de compra com cartão de crédito', 'purchase' => $creditCardPurchase]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreditCardPurchaseRequest $request, CreditCardPurchase $creditCardPurchase): JsonResponse
    {
        DB::beginTransaction();
        try {
            $creditCardPurchase->update($request->validated());

            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('credit-card-purchases.index')->with('success', 'Compra atualizada com sucesso!');

            return response()->json(['message' => 'Compra atualizada com sucesso!', 'purchase' => $creditCardPurchase]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar a compra: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao atualizar a compra. Tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditCardPurchase $creditCardPurchase): JsonResponse
    {
        DB::beginTransaction();
        try {
            $creditCardPurchase->delete();
            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('credit-card-purchases.index')->with('success', 'Compra excluída com sucesso!');

            return response()->json(['message' => 'Compra excluída com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir a compra: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao excluir a compra. Tente novamente mais tarde.'], 500);
        }
    }
}
