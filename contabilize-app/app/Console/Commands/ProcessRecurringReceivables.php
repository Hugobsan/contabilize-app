<?php

namespace App\Console\Commands;

use App\Models\AccountReceivable;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRecurringReceivables extends Command
{
    protected $signature = 'receivables:process-recurring';
    protected $description = 'Gera automaticamente receitas recorrentes com base no período de recorrência.';
    
    public function handle()
    {
        $today = Carbon::today();
        $receivables = AccountReceivable::whereNotNull('recurrence_period')
            ->whereDate('next_due_date', '<=', $today)
            ->get();

        foreach ($receivables as $receivable) {
            $newReceivable = $receivable->replicate(); // Clona a receita original
            $newReceivable->due_date = $receivable->next_due_date;
            $newReceivable->next_due_date = $this->calculateNextDueDate($receivable);
            $newReceivable->save();
        }
    }

    private function calculateNextDueDate(AccountReceivable $receivable): ?Carbon
    {
        return match ($receivable->recurrence_period) {
            'daily' => $receivable->next_due_date->addDay(),
            'weekly' => $receivable->next_due_date->addWeek(),
            'monthly' => $receivable->next_due_date->addMonth(),
            'annually' => $receivable->next_due_date->addYear(),
            default => null,
        };
    }
}
