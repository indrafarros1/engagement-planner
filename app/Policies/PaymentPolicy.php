<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewAmounts();
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->canViewAmounts();
    }

    public function create(User $user): bool
    {
        return $user->canViewAmounts();
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->canViewAmounts();
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->isOwner();
    }
}
