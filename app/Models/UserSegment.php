<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSegment extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'description',
        'conditions',
        'is_active'
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
