<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotificationTemplate;

class SaveNotifications extends Command
{
    protected $signature = 'notifications:save';
    protected $description = 'Save predefined notifications to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $notifications = [
            [
                'name' => 'Account Verified',
                'action' => 'ACCOUNT_VERIFIED',
                'subject' => [
                    'en' => 'Account Verified Successfully',
                    'np' => 'खाता सफलतापूर्वक प्रमाणित भयो'
                ],
                'email_body' => [
                    'en' => '<p>Dear {{fullname}},</p>
                            <p>Your account has been verified successfully. You can now access all features of our platform.</p>
                            <p>Thank you for choosing our service!</p>
                            <p>Best regards,<br/>{{site_name}}</p>',
                    'np' => '<p>प्रिय {{fullname}},</p>
                            <p>तपाईंको खाता सफलतापूर्वक प्रमाणित भएको छ। तपाईं अब हाम्रो प्लेटफर्मका सबै सुविधाहरू प्रयोग गर्न सक्नुहुन्छ।</p>
                            <p>हाम्रो सेवा रोज्नुभएकोमा धन्यवाद!</p>
                            <p>शुभकामना सहित,<br/>{{site_name}}</p>'
                ],
                'sms_body' => [
                    'en' => 'Dear {{fullname}}, your account has been verified successfully. You can now access all features of our platform. Thank you!',
                    'np' => 'प्रिय {{fullname}}, तपाईंको खाता सफलतापूर्वक प्रमाणित भएको छ। तपाईं अब हाम्रो प्लेटफर्मका सबै सुविधाहरू प्रयोग गर्न सक्नुहुन्छ। धन्यवाद!'
                ],
                'push_title' => [
                    'en' => 'Account Verified',
                    'np' => 'खाता प्रमाणित'
                ],
                'push_body' => [
                    'en' => 'Your account has been verified successfully',
                    'np' => 'तपाईंको खाता सफलतापूर्वक प्रमाणित भएको छ'
                ],
                'email_status' => 1,
                'sms_status' => 1, 
                'push_status' => 1,
                'email_sent_from_name' => '{{site_name}}',
                'email_sent_from_email' => '{{support_email}}',
                'sms_sent_from' => '{{site_name}}',
                'placeholders' => [
                    'fullname' => 'User Full Name',
                    'site_name' => 'Site Name',
                    'support_email' => 'Support Email'
                ]
            ]
        ];

        foreach ($notifications as $notification) {
            NotificationTemplate::updateOrCreate(
                ['action' => $notification['action']],
                $notification
            );
        }

        $this->info('Account verification notification template has been saved successfully.');
    }
}