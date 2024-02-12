<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        return $user->belongsToTeam($expense->team);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        if (
            // owner of the expense can edit it
            $user->id === $expense->user_id
            // owner of the team can edit the expense
            || $user->ownsTeam($expense->team)
            // admin of the team can edit the expense
            || ($user->belongsToTeam($expense->team) && $user->hasTeamRole($expense->team, 'admin'))) {

            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        if (
            // owner of the expense can edit it
            $user->id === $expense->user_id
            // owner of the team can edit the expense
            || $user->ownsTeam($expense->team)
            // admin of the team can edit the expense
            || ($user->belongsToTeam($expense->team) && $user->hasTeamRole($expense->team, 'admin'))) {

            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return true;
    }
}
