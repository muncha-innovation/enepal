<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Notice extends Model
{
    use HasFactory, HasTranslations;
    protected $fillable = ['title', 'content', 'image', 'is_active', 'is_private', 'user_id', 'business_id', 'is_verified'];
    protected $casts = [
        'is_active' => 'boolean',
        'is_private' => 'boolean'
    ];
    protected $translatable = ['title', 'content'];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function business() {
        return $this->belongsTo(Business::class);
    }
}
