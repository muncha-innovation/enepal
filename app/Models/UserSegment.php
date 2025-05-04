<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserSegment extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'description',
        'type',
        'is_default',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'segment_user')
            ->withTimestamps();
    }

    /**
     * Get users for a specific business in this segment
     */
    public function businessUsers()
    {
        return $this->belongsToMany(User::class, 'segment_user')
            ->withTimestamps()
            ->join('business_user', 'users.id', '=', 'business_user.user_id')
            ->where('business_user.business_id', $this->business_id);
    }

    /**
     * Scope default segments
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope active segments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope segments by business
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
