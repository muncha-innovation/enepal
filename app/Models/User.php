<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    ];

    const SuperAdmin = 'super-admin';
    const User = 'user';
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
    public function addresses(): MorphMany
    {
        return $this->morphMany(related: Address::class, name: 'addressable');
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



}
