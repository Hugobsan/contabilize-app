<?php

namespace App\Policies;

use App\Models\PurchaseInstallment;
use App\Models\User;

class PurchaseInstallmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PurchaseInstallment $installment): bool
    {
        return $user->id === $installment->creditCardPurchase->creditCard->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PurchaseInstallment $installment): bool
    {
        return $user->id === $installment->creditCardPurchase->creditCard->user_id;
    }

    public function delete(User $user, PurchaseInstallment $installment): bool
    {
        return $user->id === $installment->creditCardPurchase->creditCard->user_id;
    }

    public function restore(User $user, PurchaseInstallment $installment): bool
    {
        return $user->id === $installment->creditCardPurchase->creditCard->user_id;
    }

    public function forceDelete(User $user, PurchaseInstallment $installment): bool
    {
        return $user->id === $installment->creditCardPurchase->creditCard->user_id;
    }
}
