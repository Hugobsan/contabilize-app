<?php

namespace App\Observers;

use App\Enums\StatusEnum;
use App\Models\AccountPayable;
use App\Notifications\DueDateNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountPayableObserver
{
    public function creating(AccountPayable $accountPayable)
    {
        // Lógica adicional antes de criar o registro
        if (!$accountPayable->status) {
            $accountPayable->status = StatusEnum::PENDING;
        }
    }

    /**
     * Handle the AccountPayable "created" event.
     */
    public function created(AccountPayable $accountPayable): void
    {
        // Verifica se há uma data de vencimento e agenda a notificação
        if ($accountPayable->due_date) {
            $accountPayable->user->notify((new DueDateNotification($accountPayable))->delay($accountPayable->due_date));
        }
    }

    /**
     * Handle the AccountPayable "updated" event.
     */
    public function updated(AccountPayable $accountPayable): void
    {
        if ($accountPayable->isDirty('status') && $accountPayable->status === StatusEnum::PAID) {
            DB::beginTransaction();

            try {
                // Buscar o job que contém o ID da conta a pagar
                $job = DB::table('jobs')
                    ->where('payload', 'like', '%"account_payable_id":' . $accountPayable->id . '%')
                    ->first();

                if ($job) {
                    DB::table('failed_jobs')->insert([
                        'connection' => $job->connection,
                        'queue' => $job->queue,
                        'payload' => $job->payload,
                        'exception' => 'Job manualmente marcado como falhado devido à alteração de status.',
                        'failed_at' => Carbon::now(),
                    ]);

                    // Apagar o job da tabela `jobs`
                    DB::table('jobs')->where('id', $job->id)->delete();
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

                Log::error('Erro ao mover job para failed_jobs: ' . $e->getMessage());

                // throw $e;
            }
        }
    }

    /**
     * Handle the AccountPayable "deleted" event.
     */
    public function deleted(AccountPayable $accountPayable): void
    {
        DB::beginTransaction();

        try {
            // Buscar o job que contém o ID da conta a pagar
            $job = DB::table('jobs')
                ->where('payload', 'like', '%"account_payable_id":' . $accountPayable->id . '%')
                ->first();

            if ($job) {
                // Inserir o job na tabela `failed_jobs`
                DB::table('failed_jobs')->insert([
                    'connection' => $job->connection,
                    'queue' => $job->queue,
                    'payload' => $job->payload,
                    'exception' => 'Job manualmente marcado como falhado devido à exclusão da conta.',
                    'failed_at' => Carbon::now(),
                ]);

                // Apagar o job da tabela `jobs`
                DB::table('jobs')->where('id', $job->id)->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Erro ao mover job para failed_jobs após exclusão: ' . $e->getMessage());

            // throw $e;
        }
    }

    /**
     * Handle the AccountPayable "restored" event.
     */
    public function restored(AccountPayable $accountPayable): void
    {
        //
    }

    /**
     * Handle the AccountPayable "force deleted" event.
     */
    public function forceDeleted(AccountPayable $accountPayable): void
    {
        //
    }
}
