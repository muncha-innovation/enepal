<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'image', 'active', 'is_private', 'user_id', 'business_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function business() {
        return $this->belongsTo(Business::class);
    }
}
