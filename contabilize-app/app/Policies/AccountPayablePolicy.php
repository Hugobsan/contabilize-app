<?php

namespace App\Policies;

use App\Models\AccountPayable;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AccountPayablePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Permite que qualquer usuário autenticado visualize a lista de contas (somente as suas próprias)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AccountPayable $accountPayable): bool
    {
        // Permite que o usuário visualize apenas suas próprias contas
        return $user->id === $accountPayable->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Permite que qualquer usuário autenticado crie uma nova conta a pagar para si mesmo
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AccountPayable $accountPayable): bool
    {   
        // Permite que o usuário atualize apenas suas próprias contas
        return $user->id === $accountPayable->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AccountPayable $accountPayable): bool
    {
        // Permite que o usuário exclua apenas suas próprias contas
        return $user->id === $accountPayable->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AccountPayable $accountPayable): bool
    {
        // Permite que o usuário restaure apenas suas próprias contas
        return $user->id === $accountPayable->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AccountPayable $accountPayable): bool
    {
        // Permite que o usuário exclua permanentemente apenas suas próprias contas
        return $user->id === $accountPayable->user_id;
    }
}
