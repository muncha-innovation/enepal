<?php

namespace App\Console\Commands;

use App\Base\Notifications\emails;
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
        $emailsInstance = new emails();
        $notifications = $emailsInstance->emails;

        foreach ($notifications as $notification) {
            NotificationTemplate::updateOrCreate(
                ['action' => $notification['action']],
                $notification
            );
        }

        $this->info('Notifications have been saved successfully.');
    }
}