<?php

namespace App\Observers;

use App\Models\CreditCardPurchase;
use App\Models\PurchaseInstallment;
use Carbon\Carbon;

class CreditCardPurchaseObserver
{
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
        if ($purchase->isDirty('amount') || $purchase->isDirty('installments_count')) {
            // Recalcular parcelas
            $purchase->installments->each->delete();
            $this->created($purchase);
        }
    }

    public function deleting(CreditCardPurchase $purchase)
    {
        $purchase->installments->each->delete();
    }


}
