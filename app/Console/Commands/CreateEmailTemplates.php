<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates default email templates';

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
        
        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to our platform',
                'body' => 'Welcome to our platform. We are excited to have you on board.',
                'slug' => 'welcome_email'
            ],
            [
                'name' => 'Password Reset Email',
                'subject' => 'Password Reset',
                'body' => 'You have requested to reset your password. Click the link below to reset your password.',
                'slug' => 'password_reset_email'
            ],
            [
                'name' => 'Account Activation Email',
                'subject' => 'Activate your account',
                'body' => 'Click the link below to activate your account.',
                'slug' => 'account_activation_email'
            ],
            [
                'name' => 'Member Added to Business Email',
                'subject' => 'You have been added to a business',
                'body' => 'You have been added to a business. Your role is :role. Your password is :password',
                'slug' => 'member_added_to_business_email'
            ]
        ];

        foreach ($templates as $template) {
            if (\App\Models\EmailTemplate::where('slug', $template['slug'])->exists()) {
                continue;
            }
            \App\Models\EmailTemplate::create($template);
        }

        $this->info('Email templates created successfully');
        return 0;
    }
}
