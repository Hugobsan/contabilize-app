<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Http\Requests\StoreAccountReceivableRequest;
use App\Http\Requests\UpdateAccountReceivableRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountReceivableController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AccountReceivable::class, 'accountReceivable');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'category', 'status']);
        $accounts = AccountReceivable::where('user_id', Auth::id())
            ->filter($filters)
            ->get();

        // Comentário para uso com Inertia:
        // return Inertia::render('AccountsReceivable/Index', ['accounts' => $accounts]);

        return response()->json($accounts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('AccountsReceivable/Create');

        return response()->json(['message' => 'Form de criação de contas a receber']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountReceivableRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $accountReceivable = AccountReceivable::create($request->validated() + ['user_id' => Auth::id()]);
            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('accounts-receivable.index')->with('success', 'Conta a receber criada com sucesso!');

            return response()->json(['message' => 'Conta a receber criada com sucesso!', 'account' => $accountReceivable]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar a conta a receber: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao criar a conta a receber. Tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountReceivable $accountReceivable): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('AccountsReceivable/Show', ['account' => $accountReceivable]);

        return response()->json($accountReceivable);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountReceivable $accountReceivable): JsonResponse
    {
        // Comentário para uso com Inertia:
        // return Inertia::render('AccountsReceivable/Edit', ['account' => $accountReceivable]);

        return response()->json(['message' => 'Form de edição de contas a receber', 'account' => $accountReceivable]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountReceivableRequest $request, AccountReceivable $accountReceivable): JsonResponse
    {
        DB::beginTransaction();

        try {
            $accountReceivable->update($request->validated());
            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('accounts-receivable.index')->with('success', 'Conta a receber atualizada com sucesso!');

            return response()->json(['message' => 'Conta a receber atualizada com sucesso!', 'account' => $accountReceivable]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar a conta a receber: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao atualizar a conta a receber. Tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountReceivable $accountReceivable): JsonResponse
    {
        DB::beginTransaction();

        try {
            $accountReceivable->delete();
            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('accounts-receivable.index')->with('success', 'Conta a receber excluída com sucesso!');

            return response()->json(['message' => 'Conta a receber excluída com sucesso!']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir a conta a receber: ' . $e->getMessage());

            return response()->json(['message' => 'Erro ao excluir a conta a receber. Tente novamente mais tarde.'], 500);
        }
    }
}
