<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use PhpOffice\PhpSpreadsheet\Style\Supervisor;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'dob',
        'has_passport',
        'password',
        'phone',
        'profile_picture',
        'force_update_password',
        'last_password_updated',
        'fcm_token',
        'fcm_token_updated_at',
        'active',
        'created_by',
    ];
    
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use LogsActivity;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'datetime',
        'has_passport' => 'boolean',
        'active' => 'boolean',
    ];

    const SuperAdmin = 'super-admin';
    const User = 'user';

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getLogoAttribute(): string
    {
        return $this->profile_picture ? Storage::url($this->profile_picture) : '';
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(attributes: ['id', 'user_name', 'password', 'role'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(callback: fn(string $eventName): string => "User has been {$eventName}");
    }
    public function tapActivity(Activity $activity, string $event): void
    {
        if ($properties = $activity->properties) {
            if ($properties->has('attributes')) {
                $attributes = $properties->get('attributes');
                if (isset($attributes['password'])) {
                    $attributes['password'] = '<secret>';
                }
                $properties->put('attributes', $attributes);
            }
            if ($properties->has('old')) {
                $old = $properties->get('old');
                if (isset($old['password'])) {
                    $old['password'] = '<secret>';
                }
                $properties->put('old', $old);
            }
            $activity->properties = $properties;
        }
    }
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    public function primaryAddress(): MorphOne
    {
        return $this->morphOne(related: Address::class, name: 'addressable')->where(column: 'address_type', operator: 'primary');
    }
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isSuperAdmin()
    {
        return $this->hasRole(User::SuperAdmin);
    }

    public function hasCreated($model)
    {
        if ($model->user_id == $this->id) {
            return true;
        }
        return false;
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class);
    }


    public function getNotifications() {}

    public function preferredCategories()
    {
        return $this->belongsToMany(Category::class, 'news_preferences');
    }

    public function toggleNewsPreference($category_id)
    {
        $this->preferredCategories()->toggle($category_id);
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function newsItems()
    {
        return $this->morphMany(NewsItem::class, 'sourceable');
    }
    public function education()
    {
        return $this->hasMany(UserEducation::class);
    }
    public function workExperience()
    {
        return $this->hasMany(UserExperience::class);
    }
    public function preference() {
        return $this->hasOne(UserPreference::class);
    }
}
