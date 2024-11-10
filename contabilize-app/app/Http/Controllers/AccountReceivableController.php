<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Http\Requests\StoreAccountReceivableRequest;
use App\Http\Requests\UpdateAccountReceivableRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AccountReceivableController extends Controller
{
    /**
     * Verifica se o usuário autenticado tem permissão para acessar a rota
     * 
     * @param string $function Nome da permissão a ser verificada
     */
    protected function authorizeMe($permission, $accountPayable = AccountReceivable::class)
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
        $accounts = AccountReceivable::where('user_id', Auth::id())
            ->filter($filters)
            ->get();

        return Inertia::render('AccountsReceivable/Index', ['accounts' => $accounts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountReceivableRequest $request)
    {
        $this->authorizeMe('create');

        DB::beginTransaction();

        try {
            $accountReceivable = AccountReceivable::create($request->validated() + ['user_id' => Auth::id()]);

            // Calculo do next_due_date
            $nextDueDate = $this->calculateNextDueDate($accountReceivable);
            $accountReceivable->update(['next_due_date' => $nextDueDate]);
            $accountReceivable->save();

            DB::commit();

            // Comentário para uso com Inertia:
            return back()->with('success', 'Conta a receber criada com sucesso!');

            // return response()->json(['message' => 'Conta a receber criada com sucesso!', 'account' => $accountReceivable]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar a conta a receber: ' . $e->getMessage());

            return back()->with('error', 'Erro ao criar a conta a receber. Tente novamente mais tarde.');
            // return response()->json(['message' => 'Erro ao criar a conta a receber. Tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountReceivableRequest $request, AccountReceivable $accountReceivable)
    {
        dd($accountReceivable);
        $this->authorizeMe('update', $accountReceivable);
        DB::beginTransaction();

        try {
            $accountReceivable->update($request->validated());
            DB::commit();

            return back()->with('success', 'Conta a receber atualizada com sucesso!');
            // return response()->json(['message' => 'Conta a receber atualizada com sucesso!', 'account' => $accountReceivable]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar a conta a receber: ' . $e->getMessage());

            return back()->with('error', 'Erro ao atualizar a conta a receber. Tente novamente mais tarde.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountReceivable $accountReceivable)
    {
        DB::beginTransaction();

        try {
            $accountReceivable->delete();
            DB::commit();

            // Comentário para uso com Inertia:
            // return redirect()->route('accounts-receivable.index')->with('success', 'Conta a receber excluída com sucesso!');

            return redirect()->route('accounts-receivable.index')->with('success', 'Conta a receber excluída com sucesso!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir a conta a receber: ' . $e->getMessage());

            return back()->with('error', 'Erro ao excluir a conta a receber. Tente novamente mais tarde.');
        }
    }

    private function calculateNextDueDate(AccountReceivable $receivable): ?Carbon
    {
        return match ($receivable->recurrence_period) {
            'daily' => $receivable->due_date->addDay(),
            'weekly' => $receivable->due_date->addWeek(),
            'monthly' => $receivable->due_date->addMonth(),
            'annually' => $receivable->due_date->addYear(),
            default => null,
        };
    }
}
