<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessMember extends Model
{
    use HasFactory;
    static $ROLES = ['owner', 'admin', 'member'];
    protected $table = 'business_user';
    protected $fillable = ['business_id', 'user_id', 'role', 'position', 'is_active','has_joined'];


}
