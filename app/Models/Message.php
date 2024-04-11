<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['business_id', 'sender_id', 'recipient_id', 'content', 'type'];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver() {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    public function business() {
        return $this->belongsTo(Business::class);
    }
}
