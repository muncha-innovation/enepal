<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNewsPreference extends Model
{
    protected $fillable = ['user_id', 'category_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }
} 