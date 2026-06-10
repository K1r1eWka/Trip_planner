<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function view(User $user, Trip $trip): bool
    {
        return $user->id === $trip->owner_id || $trip->hasMember($user);
    }

    public function update(User $user, Trip $trip): bool
    {
        return $user->id === $trip->owner_id;
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $user->id === $trip->owner_id;
    }

    public function manage(User $user, Trip $trip): bool
    {
        return $user->id === $trip->owner_id;
    }
}
