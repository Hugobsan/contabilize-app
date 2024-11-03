<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Http\Requests\StoreCreditCardRequest;
use App\Http\Requests\UpdateCreditCardRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditCardController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CreditCard::class, 'creditCard');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $cards = CreditCard::where('user_id', Auth::id())->get();

        // Comentário para uso com Inertia:
        // return Inertia::render('CreditCards/Index', ['cards' => $cards]);

        return response()->json($cards);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('CreditCards/Create');

        return response()->json(['message' => 'Formulário de criação de cartão de crédito']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreditCardRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $creditCard = CreditCard::create($request->validated() + ['user_id' => Auth::id()]);

            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('credit-cards.index')->with('success', 'Cartão de crédito criado com sucesso!');

            return response()->json(['message' => 'Cartão de crédito criado com sucesso!', 'card' => $creditCard]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar o cartão de crédito: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao criar o cartão de crédito. Tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CreditCard $creditCard): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('CreditCards/Show', ['card' => $creditCard]);

        return response()->json($creditCard);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreditCard $creditCard): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('CreditCards/Edit', ['card' => $creditCard]);

        return response()->json(['message' => 'Formulário de edição de cartão de crédito', 'card' => $creditCard]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreditCardRequest $request, CreditCard $creditCard): JsonResponse
    {
        DB::beginTransaction();
        try {
            $creditCard->update($request->validated());

            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('credit-cards.index')->with('success', 'Cartão de crédito atualizado com sucesso!');

            return response()->json(['message' => 'Cartão de crédito atualizado com sucesso!', 'card' => $creditCard]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar o cartão de crédito: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao atualizar o cartão de crédito. Tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditCard $creditCard): JsonResponse
    {
        DB::beginTransaction();
        try {
            $creditCard->delete();
            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('credit-cards.index')->with('success', 'Cartão de crédito excluído com sucesso!');

            return response()->json(['message' => 'Cartão de crédito excluído com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir o cartão de crédito: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao excluir o cartão de crédito. Tente novamente mais tarde.'], 500);
        }
    }
}
