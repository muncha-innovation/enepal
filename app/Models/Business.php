<?php

namespace App\Models;

use App\Enums\SettingKeys;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Business extends Model
{
    use SoftDeletes;
    use HasFactory, HasTranslations;
    protected $fillable = ['name', 'description', 'type_id', 'created_by', 'contact_person_id', 'is_verified', 'is_featured', 'is_active', 'custom_email_message', 'established_year', 'email', 'phone_1', 'phone_2', 'website', 'logo', 'cover_image',];
    protected $translatable = ['description'];
    protected $dates = ['deleted_at'];

    static $ROLES = ['owner', 'admin', 'member'];
    static $SETTINGS = [SettingKeys::MAX_NOTIFICATION_PER_DAY, SettingKeys::MAX_NOTIFICATION_PER_MONTH];    

    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }
    public function scopeInactive($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function scopeFollowing($query): void
    {
         $query->whereHas('users', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['role', 'position', 'created_at', 'updated_at'])->withTimestamps();
    }
    
    public function notifications()
    {
        return $this->hasMany(BusinessNotification::class);
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

    public function destinations() {
        return $this->belongsToMany(Country::class, 'business_destinations')
            ->withPivot('num_people_sent')
            ->withTimestamps();
    }
    public function taughtLanguages() {
        return $this->belongsToMany(Language::class, 'business_languages')->withPivot(['price','currency'])->where('type', 'taught');
    }
    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'business_facilities')->withPivot('value');
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function hours()
    {
        return $this->hasMany(BusinessHours::class);
    }

    public function socialNetworks()
    {
        return $this->belongsToMany(SocialNetwork::class, 'business_social_networks')
            ->withPivot(['url', 'is_active'])
            ->withTimestamps();
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

    public function getFormattedHoursAttribute()
    {
        $shortDays = ['Monday' => 'Mon','Tuesday' => 'Tue','Wednesday' => 'Wed','Thursday' => 'Thu','Friday' => 'Fri','Saturday' => 'Sat','Sunday' => 'Sun'];
        $orderedDays = array_keys($shortDays);

        $allHours = $this->hours()
            ->where('is_open', true)
            ->get()
            ->sortBy(function($item) use ($orderedDays) {
                return array_search($item->day, $orderedDays);
            });

        // Helper to format times
        $fmt = function($time) { return \Carbon\Carbon::parse($time)->format('g:ia'); };

        $groups = [];
        foreach ($allHours as $hour) {
            $key = $hour->open_time.'_'.$hour->close_time;
            if (! isset($groups[$key])) {
                $groups[$key] = ['days' => [], 'open' => $hour->open_time, 'close' => $hour->close_time];
            }
            $groups[$key]['days'][] = $hour->day;
        }

        // Merge consecutive days
        $formatted = [];
        foreach ($groups as $group) {
            $days = $group['days'];
            $finalRanges = [];
            $rangeStart = $days[0];
            $prev = $rangeStart;

            for ($i = 1; $i < count($days); $i++) {
                $current = $days[$i];
                $prevIndex = array_search($prev, $orderedDays);
                $currentIndex = array_search($current, $orderedDays);
                
                // If consecutive in the day list, continue. Otherwise close that range
                if ($currentIndex !== $prevIndex + 1) {
                    $finalRanges[] = ($rangeStart === $prev) ? $shortDays[$rangeStart] : ($shortDays[$rangeStart].'-'.$shortDays[$prev]);
                    $rangeStart = $current;
                }
                $prev = $current;
            }
            // Close final range
            $finalRanges[] = ($rangeStart === $prev) ? $shortDays[$rangeStart] : ($shortDays[$rangeStart].'-'.$shortDays[$prev]);

            // Example: Mon-Fri 1:00pm to 3:00pm
            $formatted[] = implode(', ', $finalRanges).' '.$fmt($group['open']).' to '.$fmt($group['close']);
        }

        return implode('; ', $formatted);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'business_vendor')
            ->withTimestamps();
    }

    public function conversations() {
        return $this->hasMany(Conversation::class);
    }

    public function segments()
    {
        return $this->hasMany(UserSegment::class);
    }
}
