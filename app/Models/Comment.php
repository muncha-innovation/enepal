<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'comment',
        'parent_id', // For nested replies
        'is_approved' // For moderation
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function post() {
        return $this->belongsTo(Post::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    // For nested comments/replies
    public function parent() {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies() {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'replies');
    }

    // Get business through the post
    public function business() {
        return $this->hasOneThrough(Business::class, Post::class, 'id', 'id', 'post_id', 'business_id');
    }

    // Scope for approved comments
    public function scopeApproved($query) {
        return $query->where('is_approved', true);
    }

    // Scope for pending comments
    public function scopePending($query) {
        return $query->where('is_approved', false);
    }

    // Scope for top-level comments (not replies)
    public function scopeTopLevel($query) {
        return $query->whereNull('parent_id');
    }

    // Check if comment belongs to a business
    public function belongsToBusiness($businessId) {
        return $this->post->business_id == $businessId;
    }
}
