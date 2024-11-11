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
    protected function authorizeMe($permission, $accountPayable = CreditCardPurchase::class)
    {
        if (!auth()->user()->can($permission, $accountPayable)) {
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeMe('viewAny', CreditCardPurchase::class);

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
     * Store a newly created resource in storage.
     */
    public function store(StoreCreditCardPurchaseRequest $request)
    {
        $this->authorizeMe('create');

        DB::beginTransaction();
        try {
            $purchase = CreditCardPurchase::create($request->validated());
            DB::commit();

            return back()->with('success', 'Compra criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar a compra: ' . $e->getMessage());

            return back()->with('error', 'Erro ao criar a compra. Tente novamente mais tarde.');
        }
    }
}