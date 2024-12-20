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
use Inertia\Inertia;

class CreditCardController extends Controller
{
    protected function authorizeMe($permission, $accountPayable = CreditCard::class)
    {
        if (!auth()->user()->can($permission, $accountPayable)) {
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorizeMe('viewAny');

        $creditCards = CreditCard::where('user_id', Auth::id())->get();

        return Inertia::render('CreditCards/Index', ['creditCards' => $creditCards]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreditCardRequest $request)
    {
        $this->authorizeMe('create');

        DB::beginTransaction();
        try {
            $creditCard = CreditCard::create($request->validated() + ['user_id' => Auth::id()]);

            DB::commit();

            // Comentário para uso com Inertia:
            return back()->with('success', 'Cartão de crédito criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar o cartão de crédito: ' . $e->getMessage());

            return back()->with('error', 'Erro ao criar o cartão de crédito. Tente novamente mais tarde.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CreditCard $creditCard)
    {
        $this->authorizeMe('view', $creditCard);

        // Pré-carregar relação de compras com cartão de crédito
        $creditCard->load('purchases.installments');
        // Comentário para uso com Inertia:
        return Inertia::render('CreditCards/Show', ['card' => $creditCard]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreditCardRequest $request, CreditCard $creditCard)
    {
        $this->authorizeMe('update', $creditCard);
        DB::beginTransaction();
        try {
            $creditCard->update($request->validated());

            DB::commit();

            // Comentário para uso com Inertia:
            return back()->with('success', 'Cartão de crédito atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar o cartão de crédito: ' . $e->getMessage());

            return back()->with('error', 'Erro ao atualizar o cartão de crédito. Tente novamente mais tarde.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditCard $creditCard)
    {
        $this->authorizeMe('delete', $creditCard);
        DB::beginTransaction();
        try {
            $creditCard->delete();
            DB::commit();

            // Comentário para uso com Inertia:
            return redirect()->route('credit-cards.index')->with('success', 'Cartão de crédito excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir o cartão de crédito: ' . $e->getMessage());

            return back()->with('error', 'Erro ao excluir o cartão de crédito. Tente novamente mais tarde.');
        }
    }
}
