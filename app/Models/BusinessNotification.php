<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'business_id',
        'is_active',
        'is_private',
        'is_verified',
        'verified_by',
        'is_sent',
        'sent_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_private' => 'boolean',
        'is_verified' => 'boolean',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the business that owns the notification.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user who verified the notification.
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * The users that received this notification.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'business_notifications_users', 'notification_id', 'user_id')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Check if the notification has been read by a specific user.
     */
    public function isReadBy(User $user)
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivotNotNull('read_at')
            ->exists();
    }

    /**
     * Mark notification as read by a specific user.
     */
    public function markAsReadBy(User $user)
    {
        $this->users()->updateExistingPivot($user->id, [
            'read_at' => now()
        ]);
    }
}
