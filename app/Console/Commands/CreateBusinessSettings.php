<?php

namespace App\Console\Commands;

use App\Models\BusinessSetting;
use Illuminate\Console\Command;

class CreateBusinessSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes Business Settings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Initializing General Settings');
        $data = [
            [
                'key' => 'site_settings',
                'value' => json_encode([
                    'site_name' => 'Enepali',
                    'maintainence_mode' => false,
                    'secure_password' => false,
                    'max_notification_per_day' => 5,
                    'max_notification_per_month' => 5,
                ]),
                'type' => 'general',
            ],
            [
                'key' => 'email_config',
                'value' => json_encode([
                    'enabled' => true,
                    'name' => 'smtp',
                    'host' => 'smtp.mailtrap.io',
                    'port' => '2525',
                    'username' => 'username',
                    'password' => 'password',
                    'encryption' => 'tls',
                    'from_address' => 'info@enepali.com',
                    'from_name' => 'Enepali'
                ]),
                'type' => 'email',
            ],
            [
                'key' => 'email_template',
                'value' => json_encode([
                    'body' => 'Welcome to Enepali, {{username}}. We are glad to have you.',
                    'placeholders' => [
                        ['name' => 'username', 'description' => 'User Name'],
                        ['first_name' => 'First name of user'],
                        ['last_name' => 'Last Name of user'],
                        ['site_name' => 'Name of website']
                    ]
                ]),
                'type' => 'template',
            ],
            [
                'key' => 'sms_template',
                'value' => json_encode([
                    'subject' => 'Welcome to Enepali',
                    'body' => 'Welcome to Enepali, {{username}}. We are glad to have you.'
                ]),
                'type' => 'template',
            ]
        ];
        foreach ($data as $setting) {
            BusinessSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
        $this->info('General Settings Initialized');
    }
}
