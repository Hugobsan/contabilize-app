<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\AccountPayable;

class DueDateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $accountPayable;

    public function __construct(AccountPayable $accountPayable)
    {
        $this->accountPayable = $accountPayable;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Lembrete de Vencimento de Conta')
            ->line('Sua conta de ' . $this->accountPayable->description . ' vence em ' . $this->accountPayable->due_date->format('d/m/Y') . '.')
            ->action('Pagar Agora', url('/accounts-payable/' . $this->accountPayable->id))
            ->line('Esta é uma mensagem agendada. Se você já efetuou o pagamento, desconsidere.');
    }

    public function toDatabase($notifiable)
{
    return [
        'description' => $this->accountPayable->description,
        'due_date' => $this->accountPayable->due_date->format('d/m/Y'),
        'status' => $this->accountPayable->status,
        'user_id' => $this->accountPayable->user_id,
    ];
}

    public function toArray($notifiable)
    {
        return [
            'description' => $this->accountPayable->description,
            'due_date' => $this->accountPayable->due_date,
            'status' => $this->accountPayable->status,
            'user_id' => $this->accountPayable->user_id,
        ];
    }
}
