<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function getIsPreferredAttribute() {
        if (!auth()->check()) {
            return false;
        }
        return auth()->user()->preferredCategories->contains($this);

    }
}
