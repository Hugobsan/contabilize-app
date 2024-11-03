<?php

namespace App\Policies;

use App\Models\CreditCard;
use App\Models\User;

class CreditCardPolicy
{
    /**
     * Determine whether the user can view any credit cards.
     */
    public function viewAny(User $user): bool
    {
        return true; // Permite que todos os usuários visualizem cartões, ajustável conforme necessário.
    }

    /**
     * Determine whether the user can view a specific credit card.
     */
    public function view(User $user, CreditCard $creditCard): bool
    {
        return $user->id === $creditCard->user_id;
    }

    /**
     * Determine whether the user can create a credit card.
     */
    public function create(User $user): bool
    {
        return true; // Ajuste conforme as regras de negócios.
    }

    /**
     * Determine whether the user can update a specific credit card.
     */
    public function update(User $user, CreditCard $creditCard): bool
    {
        return $user->id === $creditCard->user_id;
    }

    /**
     * Determine whether the user can delete a specific credit card.
     */
    public function delete(User $user, CreditCard $creditCard): bool
    {
        return $user->id === $creditCard->user_id;
    }

    /**
     * Determine whether the user can restore a deleted credit card.
     */
    public function restore(User $user, CreditCard $creditCard): bool
    {
        return $user->id === $creditCard->user_id;
    }

    /**
     * Determine whether the user can permanently delete a specific credit card.
     */
    public function forceDelete(User $user, CreditCard $creditCard): bool
    {
        return $user->id === $creditCard->user_id;
    }
}
