<?php

namespace App\Http\Controllers;

use App\Models\AccountPayable;
use App\Http\Requests\StoreAccountPayableRequest;
use App\Http\Requests\UpdateAccountPayableRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AccountPayableController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AccountPayable::class, 'accountPayable');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'status']);
        $accounts = AccountPayable::where('user_id', Auth::id())
            ->filter($filters)
            ->get();

        return Inertia::render('AccountsPayable/Index', ['accounts' => $accounts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('AccountsPayable/Create');

        return response()->json(['message' => 'Form de criação de contas a pagar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountPayableRequest $request)
    {
        DB::beginTransaction();

        try {
            // Criação da conta a pagar
            $accountPayable = AccountPayable::create($request->validated() + ['user_id' => Auth::id()]);

            DB::commit();

            return redirect()->route('accounts.index')->with('success', 'Conta a pagar criada com sucesso!');

            // return response()->json(['message' => 'Conta a pagar criada com sucesso!', 'account' => $accountPayable]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Erro ao criar a conta a pagar: ' . $e->getMessage());

            return redirect ()->route('accounts.index')->with('error', 'Erro ao criar a conta a pagar. Tente novamente mais tarde.');
            // return response()->json(['message' => 'Erro ao criar a conta a pagar. Tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountPayable $accountPayable): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('AccountsPayable/Show', ['account' => $accountPayable]);

        return response()->json($accountPayable);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountPayable $accountPayable): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('AccountsPayable/Edit', ['account' => $accountPayable]);

        return response()->json(['message' => 'Form de edição de contas a pagar', 'account' => $accountPayable]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountPayableRequest $request, AccountPayable $accountPayable)
    {
        dd("Controller");
        DB::beginTransaction();

        try {
            $accountPayable->update($request->validated());

            DB::commit();

            // Comentário para uso com Inertia:
            return redirect()->back()->with('success', 'Conta a pagar atualizada com sucesso!');

            // return response()->json(['message' => 'Conta a pagar atualizada com sucesso!', 'account' => $accountPayable]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar a conta a pagar: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Erro ao atualizar a conta a pagar. Tente novamente mais tarde.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountPayable $accountPayable)
    {
        DB::beginTransaction();
        try {
            $accountPayable->delete();
            DB::commit();

            // Comentário para uso com Inertia:
            return redirect()->route('accounts-payable.index')->with('success', 'Conta a pagar excluída com sucesso!');

            // return response()->json(['message' => 'Conta a pagar excluída com sucesso!']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir a conta a pagar: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Erro ao excluir a conta a pagar. Tente novamente mais tarde.');
        }
    }
}
