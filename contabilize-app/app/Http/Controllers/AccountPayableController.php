<?php

namespace App\Http\Controllers;

use App\Models\AccountPayable;
use App\Http\Requests\StoreAccountPayableRequest;
use App\Http\Requests\UpdateAccountPayableRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AccountPayableController extends Controller
{
    /**
     * Verifica se o usuário autenticado tem permissão para acessar a rota
     * 
     * @param string $function Nome da permissão a ser verificada
     */
    protected function authorizeMe($permission, $accountPayable = AccountPayable::class)
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

        $filters = $request->only(['search', 'category', 'status']);
        $accounts = AccountPayable::where('user_id', Auth::id())
            ->filter($filters)
            ->get();

        return Inertia::render('AccountsPayable/Index', ['accounts' => $accounts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountPayableRequest $request)
    {
        $this->authorizeMe('create');

        DB::beginTransaction();

        try {
            // Criação da conta a pagar
            $accountPayable = AccountPayable::create($request->validated() + ['user_id' => Auth::id()]);

            DB::commit();

            return back()->with('success', 'Conta a pagar criada com sucesso!');

            // return response()->json(['message' => 'Conta a pagar criada com sucesso!', 'account' => $accountPayable]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Erro ao criar a conta a pagar: ' . $e->getMessage());

            return back()->with('error', 'Erro ao criar a conta a pagar. Tente novamente mais tarde.');
            // return response()->json(['message' => 'Erro ao criar a conta a pagar. Tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountPayableRequest $request, $id)
    {
        $accountPayable = AccountPayable::find($id);

        $this->authorizeMe('update', $accountPayable);

        DB::beginTransaction();

        try {
            $update = $accountPayable->update($request->validated());
            DB::commit();

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
    public function destroy($id)
    {
        $accountPayable = AccountPayable::find($id);

        $this->authorizeMe('delete', $accountPayable);

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
