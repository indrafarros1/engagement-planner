<?php

namespace App\Policies;

use App\Models\BudgetItem;
use App\Models\User;

class BudgetItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewAmounts();
    }

    public function view(User $user, BudgetItem $item): bool
    {
        return $user->canViewAmounts();
    }

    public function create(User $user): bool
    {
        return $user->canViewAmounts();
    }

    public function update(User $user, BudgetItem $item): bool
    {
        return $user->canViewAmounts();
    }

    public function delete(User $user, BudgetItem $item): bool
    {
        return $user->isOwner();
    }

    public function export(User $user): bool
    {
        return $user->canViewAmounts();
    }

    public function import(User $user): bool
    {
        return $user->isOwner();
    }
}
