<?php

namespace App\Console\Commands;

use App\Models\PurchaseInstallment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OverdueInstallmentNotification;

class NotifyOverdueInstallments extends Command
{
    protected $signature = 'notify:overdue-installments';
    protected $description = 'Notifica os usuários sobre parcelas atrasadas.';

    public function handle()
    {
        $overdueInstallments = PurchaseInstallment::where('status', false)
            ->whereDate('due_date', '<', now())
            ->with('user')
            ->get();

        foreach ($overdueInstallments as $installment) {
            $installment->user->notify(new OverdueInstallmentNotification($installment));
        }

        $this->info('Notificações de parcelas atrasadas enviadas.');
    }
}
