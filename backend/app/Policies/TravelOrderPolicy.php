<?php

namespace App\Policies;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Models\User;

class TravelOrderPolicy
{
    /**
     * Perform pre-authorization for admin Users
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_admin) {
            return true;
        }
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TravelOrder $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TravelOrder $order): bool
    {
        return $user->id === $order->user_id && $order->status === TravelOrderStatus::Pending;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TravelOrder $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TravelOrder $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TravelOrder $order): bool
    {
        return false;
    }
}
