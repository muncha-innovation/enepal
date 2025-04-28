<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'user_type',
        'countries',
        'departure_date',
        'study_field',
        'app_language',
        'known_languages',
        'has_passport',
        'passport_expiry',
        'receive_notifications',
        'show_personalized_content',
        'distance_unit'
    ];
    
    protected $casts = [
        'departure_date' => 'datetime',
        'countries' => 'json',
        'known_languages' => 'json',
        'has_passport' => 'boolean',
        'passport_expiry' => 'date',
        'receive_notifications' => 'boolean',
        'show_personalized_content' => 'boolean'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
}
