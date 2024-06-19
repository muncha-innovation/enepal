<?php

namespace App\Models;

use App\Enums\SettingKeys;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class Business extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['description'];

    static $ROLES = ['owner', 'admin', 'member'];
    static $SETTINGS = [SettingKeys::MAX_NOTIFICATION_PER_DAY, SettingKeys::MAX_NOTIFICATION_PER_MONTH];    

    protected $guarded = [];

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['role', 'position']);
    }
    public function type()
    {
        return $this->belongsTo(BusinessType::class, 'type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function contactPerson()
    {
        return $this->belongsTo(User::class, 'contact_person_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'business_id');
    }
    public function settings() {
        return $this->hasMany(BusinessSetting::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function notices()
    {
        return $this->hasMany(Notice::class);
    }

    
    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'business_facilities');
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function getHasFollowedAttribute() {
        
        $user = $this->users()->where('user_id', auth()->id())->first();
        return $user && $user->pivot->role!=null;
    }

    public function getIsAdminAttribute() {
        $user = $this->users()->where('user_id', auth()->id())->first();
        return $user && $user->pivot->role === 'admin';
    }

    public function getIsOwnerAttribute() {
        $user = $this->users()->where('user_id', auth()->id())->first();
        return $user && $user->pivot->role === 'owner';
    }

    public function canCreateNotice() {
        $notification = $this->notices()->where('created_at', '>=', now()->startOfDay())->count();
        $maxNotificationPerDay = $this->settings()->where('key', SettingKeys::MAX_NOTIFICATION_PER_DAY)->first();
        if($maxNotificationPerDay) {
            if($notification >= $maxNotificationPerDay->value) {
                return false;
            }
        }
        $notification = $this->notices()->where('created_at', '>=', now()->startOfMonth())->count();
        $maxNotificationPerMonth = $this->settings()->where('key', SettingKeys::MAX_NOTIFICATION_PER_MONTH)->first();
        if($maxNotificationPerMonth) {
            if($notification >= $maxNotificationPerMonth->value) {
                return false;
            }
        }
        return true;
    }
}
