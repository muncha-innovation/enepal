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
                    '<p>Dear &lt;Name&gt;,</p><p><br></p><p>Namaste and welcome to Enepali. </p><p><br></p><ul><li style="text-align: left;">We are thrilled to have you join our community of Nepalese living abroad. Our mission is to connect you to with businesses and news that matter to you, no matter where you are in the world.</li></ul><p><br></p><p>What We Offer:</p><p>Local Businesses: Discover Nepalese-owned businesses and services catering to you in your area.</p><p>New Updates: Stay informed with the latest news and events from Nepal and the Nepalese community around you.</p><p><br></p><p>We are committed to providing you with a platform that keeps you connected to your roots while helping you thrive in your new environment. Your feedback is invaluable to us, so please feel free to share your thoughts and suggestions.</p><p><br></p><p>Thank you for being a part of our growing family. We look forward to serving you and making your experience with Enepali enriching and enjoyable.</p><p><br></p><p>Warm regards,</p><p><br></p><p>...</p><p>Enepali.com</p><p><br></p><p>ps: If you haven\'t done so yet, you can download our app through the links below:</p><p><span style="background-color: transparent; color: inherit; font-family: inherit; font-size: 16px;"><br></span></p><p><span style="background-color: transparent; color: inherit; font-family: inherit; font-size: 16px;">Download for Android</span></p><p><span style="background-color: transparent; color: inherit; font-family: inherit; font-size: 16px;"><br></span><span style="background-color: transparent; color: inherit; font-family: inherit; font-size: 16px;">Download for iOS</span></p><p><br></p><ul><li><br></li></ul>',
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
        SettingKeys::MEMBER_ADDED_TO_BUSINESS_EMAIL => [
            'action' => SettingKeys::MEMBER_ADDED_TO_BUSINESS_EMAIL,
            'name' => 'Member Added to Business Email',
            'email_status' => 1,
            'sms_status' => 0,
            'push_status' => 0,
            'email_sent_from_name' => '{{site_name}}',
            'email_sent_from_email' => '{{support_email}}',
            'sms_sent_from' => '{{site_name}}',
            'placeholders' => [
                'last_name' => 'User last name',
                'first_name' => 'User first name',
                'business_name' => 'Business Name',
                'android_app_link' => 'Android App Link',
                'ios_app_link' => 'iOS App Link',
            ],
            'subject' => [
                'en'=> 'Welcome to {{business_name}} on Enepali - Exclusive Deals and Community Access Await!',
                'np'=> 'Welcome to {{business_name}} on Enepali - Exclusive Deals and Community Access Await!',
            ],
            'email_body' => [
                'en' => '<p>Dear [Customer\'s Name],</p><p><span style="background-color: rgb(255 255 255 / var(--tw-bg-opacity));">Namaste!</span></p><p><span style="background-color: rgb(255 255 255 / var(--tw-bg-opacity));"><br></span></p><p>We are excited to inform you that [Business Name] has added you as a valued member of our community on [Your Portal Name]. As a member, you now have access to exclusive deals and offers tailored just for you.<span style="background-color: rgb(255 255 255 / var(--tw-bg-opacity));"></span></p><p><br></p><p><strong>What You Get as a Member:</strong></p><p><strong><br></strong></p><ul><li><p><strong>Exclusive Deals:</strong> Enjoy special discounts and offers from [Business Name].</p><p><br></p></li><li><p><span style="background-color: rgb(255 255 255 / var(--tw-bg-opacity));">[Business name] is part of a broader community within Enepali, a service dedicated to service the Nepali community living abroad.&nbsp;</span><span style="background-color: rgb(255 255 255 / var(--tw-bg-opacity));">Connect with the broader Enepali community and access a range of services designed to keep you connected to your roots.</span></p><p><br></p><p><strong>Your Account Details:</strong></p><ul><li><p><strong>Username:</strong> [Customer\'s Email]</p></li><li><p><strong>Password:</strong> [Auto-Generated Password] (You can change this password anytime in your account settings.)</p><p><b>Role :&nbsp;</b></p></li></ul></li></ul><p><br></p><p><strong>Download Our App:</strong> ◀
                To make your experience even more seamless, download the Enepali app on your mobile device:</p><ul><li><p><button type="button" initial="start" animate="end" variants="[object Object]" custom="0">Download for Android</button></p></li><li><p><button type="button" initial="start" animate="end" variants="[object Object]" custom="0">Download for iOS</button></p><p><button type="button" initial="start" animate="end" variants="[object Object]" custom="0"></button></p></li></ul><p>We are thrilled to have you with us and look forward to providing you with the best experiences and services. If you ever wish to unsubscribe from [Business Name]\'s member list, you can do so at any time by going to the My Preferences section.</p><p><br></p><p>Thank you for being a part of our community!</p><p>Warm regards,</p><p>......</p><p>Enepali.com</p><p><br></p>',
                'np' => '',
            ],
        ],
    ];
}
