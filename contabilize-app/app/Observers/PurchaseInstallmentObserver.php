<?php

namespace App\Observers;

use App\Models\PurchaseInstallment;
use App\Notifications\InstallmentDueNotification;

class PurchaseInstallmentObserver
{
    public function created(PurchaseInstallment $installment)
    {
        $installment->user->notify(
            (new InstallmentDueNotification($installment))->delay($installment->due_date->subDay())
        );
    }
}
