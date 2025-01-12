<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the database';

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
        // ask confirmation
        if (!$this->confirm('Are you sure you want to refresh the database?')) {
            return 0;
        }

        $this->call('migrate:fresh');
        $this->call('roles:seed');
        $this->call('settings:init');
        $this->call('notifications:save');
        $this->call('categories:make');
        $this->call('fetch:news');
        $this->call('seed:age');
        $this->call('seed:gender');
        $this->call('db:seed');
        return 0;
    }
}
