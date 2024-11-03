<?php

namespace App\Observers;

use App\Models\CreditCardPurchase;
use App\Models\PurchaseInstallment;
use Carbon\Carbon;

class CreditCardPurchaseObserver
{
    public function creating(CreditCardPurchase $purchase)
    {
        $creditCard = $purchase->creditCard;

        // Verifica se o limite disponível é suficiente
        if ($creditCard->available_limit < $purchase->amount) {
            throw new \Exception('Limite disponível insuficiente no cartão.');
        }

        // Reduz o limite disponível
        $creditCard->available_limit -= $purchase->amount;
        $creditCard->save();
    }

    public function created(CreditCardPurchase $purchase)
    {
        $dueDate = Carbon::parse($purchase->purchase_date);
        $installmentAmount = $purchase->amount / $purchase->installments_count;

        for ($i = 1; $i <= $purchase->installments_count; $i++) {
            PurchaseInstallment::create([
                'credit_card_purchase_id' => $purchase->id,
                'installment_number' => $i,
                'due_date' => $dueDate->copy()->addMonths($i - 1),
                'amount' => $installmentAmount,
                'status' => false,
            ]);
        }
    }

    public function updating(CreditCardPurchase $purchase)
    {
        $originalAmount = $purchase->getOriginal('amount');
        $creditCard = $purchase->creditCard;

        if ($purchase->isDirty('amount')) {
            $amountDifference = $purchase->amount - $originalAmount;

            if ($amountDifference > 0 && $creditCard->available_limit < $amountDifference) {
                throw new \Exception('Limite disponível insuficiente para aumentar o valor da compra.');
            }

            // Atualiza o limite disponível
            $creditCard->available_limit -= $amountDifference;
            $creditCard->save();
        }

        if ($purchase->isDirty('amount') || $purchase->isDirty('installments_count')) {
            $purchase->installments->each->delete();
            $this->created($purchase);
        }
    }

    public function deleted(CreditCardPurchase $purchase)
    {
        $creditCard = $purchase->creditCard;

        // Reajusta o limite disponível ao excluir a compra
        $creditCard->available_limit += $purchase->amount;
        $creditCard->save();

        $purchase->installments->each->delete();
    }

    public function restored(CreditCardPurchase $purchase)
    {
        $creditCard = $purchase->creditCard;

        // Verifica se o limite disponível é suficiente para restaurar a compra
        if ($creditCard->available_limit < $purchase->amount) {
            throw new \Exception('Limite disponível insuficiente para restaurar a compra.');
        }

        // Reduz o limite disponível novamente
        $creditCard->available_limit -= $purchase->amount;
        $creditCard->save();
    }

    public function forceDeleted(CreditCardPurchase $purchase)
    {
        $creditCard = $purchase->creditCard;

        // Reajusta o limite disponível ao forçar a exclusão da compra
        $creditCard->available_limit += $purchase->amount;
        $creditCard->save();

        $purchase->installments->each->delete();
    }
}
