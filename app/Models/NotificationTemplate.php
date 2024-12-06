<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory, \Spatie\Translatable\HasTranslations;

    protected $translatable = ['subject', 'push_title', 'email_body', 'sms_body', 'push_body'];
    protected $casts = [
        'placeholders' => 'array',
    ];
    protected $fillable = [
        'action',
        'name',
        'subject',
        'push_title',
        'email_body',
        'sms_body',
        'push_body',
        'placeholders',
        'email_status',
        'sms_status',
        'push_status',
        'email_sent_from_name',
        'email_sent_from_email',
        'sms_sent_from',
        'allow_business_section',
    ];
}