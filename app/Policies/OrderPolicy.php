<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return match($user->role) {
            'superadmin' => true,
            'dispatcher', 'taxoparkadmin' => $user->dispatcher?->taxopark_id === $order->route->taxopark_from_id,
            'driver' => $order->driver_id === $user->driver?->id,
            'client' => $order->client_id === $user->client?->id,
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return match($user->role) {
            'superadmin' => true,
            'taxoparkadmin' => true,
            'client' => true,
            default => false,
        };
    }

    public function creating(User $user, $route): bool
    {
        return match($user->role) {
            'superadmin' => true,
            'dispatcher', 'taxoparkadmin' => $user->taxoParkAdmin?->taxopark_id === $route->taxopark_from_id,
            'client' => true,
            default => false,
        };
    }

    public function update(User $user, Order $order): bool
    {
        return match($user->role) {
            'superadmin' => true,
            'dispatcher', 'taxoparkadmin' => $user->dispatcher?->taxopark_id === $order->route->taxopark_from_id,
            default => false,
        };
    }

    public function delete(User $user, Order $order): bool
    {
        return match($user->role) {
            'superadmin' => true,
            'dispatcher', 'taxoparkadmin' => $user->dispatcher?->taxopark_id === $order->route->taxopark_from_id,
            default => false,
        };
    }
}