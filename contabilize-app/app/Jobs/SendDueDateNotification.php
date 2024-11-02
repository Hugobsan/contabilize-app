<?php

namespace App\Jobs;

use App\Models\AccountPayable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DueDateNotification;

class SendDueDateNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    protected $accountPayable;

    public function __construct(AccountPayable $accountPayable)
    {
        $this->accountPayable = $accountPayable;
    }

    public function handle()
    {
        // Enviar a notificação para o usuário associado
        Notification::send($this->accountPayable->user, new DueDateNotification($this->accountPayable));
    }
}
