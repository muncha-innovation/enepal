<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;
    protected $fillable = [
        'countries',
        'departure_date',
        'study_field',
        'app_language',
    ];
    
    protected $casts = [
        'departure_date' => 'datetime',
        'countries' => 'json'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
