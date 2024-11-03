<?php

namespace App\Policies;

use App\Models\CreditCardPurchase;
use App\Models\User;

class CreditCardPurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CreditCardPurchase $purchase): bool
    {
        return $user->id === $purchase->creditCard->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CreditCardPurchase $purchase): bool
    {
        return $user->id === $purchase->creditCard->user_id;
    }

    public function delete(User $user, CreditCardPurchase $purchase): bool
    {
        return $user->id === $purchase->creditCard->user_id;
    }

    public function restore(User $user, CreditCardPurchase $purchase): bool
    {
        return $user->id === $purchase->creditCard->user_id;
    }

    public function forceDelete(User $user, CreditCardPurchase $purchase): bool
    {
        return $user->id === $purchase->creditCard->user_id;
    }
}
