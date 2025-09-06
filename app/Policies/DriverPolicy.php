<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Driver;

class DriverPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        return null;
    }

    public function view(User $user, Driver $driver): bool
    {
        return $user->dispatcher
            && $driver->taxopark_id === $user->dispatcher->taxopark_id;
    }

    public function create(User $user): bool
    {
        return (bool) $user->dispatcher;
    }

    public function update(User $user, Driver $driver): bool
    {
        return $user->dispatcher 
            && $driver->taxopark_id === $user->dispatcher->taxopark_id;
    }

    public function delete(User $user, Driver $driver): bool
    {
        return false;
    }
}