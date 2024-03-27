<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    const SuperAdmin = 'Super admin';
    const Supervisor = 'Supervisor';
    const Inspector = 'Inspector';
    const User = 'User';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id', 'user_name', 'password', 'role'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "User has been {$eventName}");
    }
    public function tapActivity(Activity $activity, string $event)
    {
        /** @var Collection $properties */
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
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }
    public function getFullPathAttribute()
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        }
        return asset('default_image.png');
    }
    
    public function getPFullNameAttribute(): string
    {
        return $this->p_last_name . ' ' . $this->p_first_name;
    }
    public function getFullNameAttribute(): string
    {
        return $this->last_name . ' ' . $this->first_name;
    }
    public function isSupervisorOrAdmin() {
        return true;
        $roles = $this->roles()->pluck('name');
        $isSupervisorOrAdmin = in_array($roles->first(), [User::SuperAdmin, User::Supervisor]);
        return $isSupervisorOrAdmin;
    }

    public function isSupervisor() {
        return true;
        $roles = $this->roles()->pluck('name');
        $isSupervisor = in_array($roles->first(), [User::Supervisor]);
        return $isSupervisor;
    }
    public function isSuperAdmin()
    {
        return true;
        $roles = $this->roles()->pluck('name');
        $isSuperAdmin = in_array($roles->first(), [User::SuperAdmin]);
        return $isSuperAdmin;
    }

    public function isUser() {
        $roles = $this->roles()->pluck('name');
        $isUser = in_array($roles->first(), [User::User]);
        return $isUser;
    }
    public function isInspector() {
        $roles = $this->roles()->pluck('name');
        $isUser = in_array($roles->first(), [User::Inspector]);
        return $isUser;
    }
    public function hasCreated($model)
    {
        if ($model->user_id == $this->id) {
            return true;
        }
        return false;
    }

}