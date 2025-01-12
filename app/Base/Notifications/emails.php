<?php

namespace App\Base\Notifications;

use App\Enums\SettingKeys;

class emails
{
    public $emails = [
        SettingKeys::WELCOME_EMAIL => [
            'name' => 'Welcome Email',
            'action' => SettingKeys::WELCOME_EMAIL,
            'email_status' => 1,
            'sms_status' => 0,
            'push_status' => 0,
            'email_sent_from_name' => '{{site_name}}',
            'email_sent_from_email' => '{{support_email}}',
            'sms_sent_from' => '{{site_name}}',
            'placeholders' => [
                'first_name' => 'User First Name',
                'last_name' => 'User last name',
                'android_app_link' => 'Android App Link',
                'ios_app_link' => 'iOS App Link',
            ],
            'subject' => [
                'en' => 'Welcome to Enepali - Your Gateway to Nepalese Community and News!',
                'np' => 'Welcome to Enepali - Your Gateway to Nepalese Community and News!',
            ],
            'email_body' => [
                'en' =>
                '<p>
                    Dear {{first_name}}
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    Namaste and welcome to Enepali.
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    We are thrilled to have you join our community of Nepalese living abroad. Our mission is to connect you to with businesses and news that matter to you, no matter where you are in the world.
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    Your password is {{password}}
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    What We Offer:
                    </p>
                    <p>
                    Local Businesses: Discover Nepalese-owned businesses and services catering to you in your area.
                    </p>
                    <p>
                    New Updates: Stay informed with the latest news and events from Nepal and the Nepalese community around you.
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    We are committed to providing you with a platform that keeps you connected to your roots while helping you thrive in your new environment. Your feedback is invaluable to us, so please feel free to share your thoughts and suggestions.
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    Thank you for being a part of our growing family. We look forward to serving you and making your experience with Enepali enriching and enjoyable.
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    Warm regards,
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    ...
                    </p>
                    <p>
                    Enepali.com
                    </p>
                    <p>
                    &nbsp;
                    </p>
                    <p>
                    ps: If you haven\'t done so yet, you can download our app through the links below:
                    </p>
                    <p>
                    <br>
                    &nbsp;
                    </p>
                    <p>
                    <span style="background-color:transparent;color:inherit;font-family:inherit;font-size:16px;">Download for Android</span>
                    </p>
                    <p>
                    <br>
                    <span style="background-color:transparent;color:inherit;font-family:inherit;font-size:16px;">Download for iOS</span>
                    </p>',
                'np' => '',
            ],
        ],
        SettingKeys::PASSWORD_RESET_EMAIL => [
            'placeholders' => [
                'first_name' => 'User First Name',
                'last_name' => 'User last name',
                'reset_password_link' => 'Reset Password Link',
            ],

            'name' => 'Reset Password Email',
            'action' => SettingKeys::PASSWORD_RESET_EMAIL,

            'email_status' => 1,
            'sms_status' => 0,
            'push_status' => 0,
            'subject' => [
                'en' => 'Enepali - Password Reset',
                'np' => 'Enepali - Password Reset',
            ],
            'email_body' => [
                'en' => '<p>Dear &lt;Name&gt;,</p><p><br></p><p>You have requested to reset your password. </p><p><br></p><p>Click the link below to reset your password.</p><p><br></p><p>With warm regards.</p><p><br></p><p>Enepali.com Team</p>',
                'np' => '',
            ],
        ],
        SettingKeys::NEW_MEMBER_INSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL => [
            'action' => SettingKeys::NEW_MEMBER_INSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL,
            'name' => 'New Member Inside Nepal Added to Business Email',
            'email_status' => 1,
            'sms_status' => 0,
            'push_status' => 0,
            'email_sent_from_name' => '{{site_name}}',
            'email_sent_from_email' => '{{support_email}}',
            'sms_sent_from' => '{{site_name}}',
            'placeholders' => [
                'business_name' => 'Business Name',
                'site_name' => 'Site Name',
                'role' => 'Role',
                'password' => 'Password',
                'business_message' => 'Business Message',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'email' => 'Email',
            ],
            'subject' => [
                'en' => '{{business_name}} - New Member Added',
                'np' => '{{business_name}} - New Member Added',
            ],
            'email_body' => [
                'en' => '',
                'np' => '',
            ],
        ],
        SettingKeys::EXISTING_MEMBER_INSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL => [
            'action' => SettingKeys::EXISTING_MEMBER_INSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL,
            'name' => 'Existing Member Inside Nepal Added to Business Email',
            'email_status' => 1,
            'sms_status' => 0,
            'push_status' => 0,
            'email_sent_from_name' => '{{site_name}}',
            'email_sent_from_email' => '{{support_email}}',
            'sms_sent_from' => '{{site_name}}',
            'placeholders' => [
                'business_name' => 'Business Name',
                'site_name' => 'Site Name',
                'role' => 'Role',
                'business_message' => 'Business Message',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'email' => 'Email',
            ],
            'subject' => [
                'en' => '{{business_name}} - Existing Member Added',
                'np' => '{{business_name}} - Existing Member Added',
            ],
            'email_body' => [
                'en' => '',
                'np' => '',
            ],
        ],
        SettingKeys::NEW_MEMBER_OUTSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL => [
            'action' => SettingKeys::NEW_MEMBER_OUTSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL,
            'name' => 'New Member Outside Nepal Added to Business Email',
            'email_status' => 1,
            'sms_status' => 0,
            'push_status' => 0,
            'email_sent_from_name' => '{{site_name}}',
            'email_sent_from_email' => '{{support_email}}',
            'sms_sent_from' => '{{site_name}}',
            'placeholders' => [
                'business_name' => 'Business Name',
                'site_name' => 'Site Name',
                'role' => 'Role',
                'password' => 'Password',
                'business_message' => 'Business Message',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'email' => 'Email',
            ],
            'subject' => [
                'en' => '{{business_name}} - New Member Added',
                'np' => '{{business_name}} - New Member Added',
            ],
            'email_body' => [
                'en' => '',
                'np' => '',
            ],
        ],
        SettingKeys::EXISTING_MEMBER_OUTSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL => [
            'action' => SettingKeys::EXISTING_MEMBER_OUTSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL,
            'name' => 'Existing Member Outside Nepal Added to Business Email',
            'email_status' => 1,
            'sms_status' => 0,
            'push_status' => 0,
            'email_sent_from_name' => '{{site_name}}',
            'email_sent_from_email' => '{{support_email}}',
            'sms_sent_from' => '{{site_name}}',
            'placeholders' => [
                'business_name' => 'Business Name',
                'site_name' => 'Site Name',
                'role' => 'Role',
                'business_message' => 'Business Message',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'email' => 'Email',
            ],
            'subject' => [
                'en' => '{{business_name}} - Existing Member Added',
                'np' => '{{business_name}} - Existing Member Added',
            ],
            'email_body' => [
                'en' => '',
                'np' => '',
            ],

        ],
        SettingKeys::BUSINESS_VERIFICATION_EMAIL => [
            'action' => SettingKeys::BUSINESS_VERIFICATION_EMAIL,
            'name' => 'Business Verification Email',
            'email_status' => 1,
            'sms_status' => 0,
            'push_status' => 0,
            'email_sent_from_name' => '{{site_name}}',
            'email_sent_from_email' => '{{support_email}}',
            'sms_sent_from' => '{{site_name}}',
            'placeholders' => [
                'business_name' => 'Business Name',
                'site_name' => 'Site Name',
            ],
            'subject' => [
                'en' => '{{business_name}} - Business Verification',
                'np' => '{{business_name}} - Business Verification',
            ],
            'email_body' => [
                'en' => '',
                'np' => '',
            ],
        ],
    ];
}
