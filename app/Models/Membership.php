<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Membership extends Pivot
{
 
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isOwner();
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}

