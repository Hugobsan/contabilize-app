<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PurchaseInstallment;
use Carbon\Carbon;

class OverdueInstallmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $installment;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseInstallment $installment)
    {
        $this->installment = $installment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dueDateFormatted = Carbon::parse($this->installment->due_date)->format('d/m/Y');

        return (new MailMessage)
                    ->subject('Aviso: Parcela em Atraso')
                    ->line("A parcela número {$this->installment->installment_number} da compra \"{$this->installment->creditCardPurchase->description}\" está em atraso desde {$dueDateFormatted}.")
                    ->action('Ver Detalhes', url('/installments/' . $this->installment->id))
                    ->line('Por favor, realize o pagamento o mais rápido possível para evitar mais consequências.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'installment_id' => $this->installment->id,
            'credit_card_purchase_id' => $this->installment->credit_card_purchase_id,
            'description' => $this->installment->creditCardPurchase->description,
            'installment_number' => $this->installment->installment_number,
            'due_date' => $this->installment->due_date->format('d/m/Y'),
            'amount' => $this->installment->amount,
            'status' => 'Atrasada',
        ];
    }
}
