<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExperience extends Model
{
    protected $fillable = ['user_id', 'job_title', 'company', 'start_date', 'end_date', 'description'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    // Inverse Relationship: Experience → User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
