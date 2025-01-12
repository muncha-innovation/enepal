<?php

namespace App\Console\Commands;

use App\Models\AgeGroup;
use Illuminate\Console\Command;

class SeedAgeGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:age';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fill default age groups';

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
        $ageGroups = AgeGroup::all();
        if($ageGroups->count() <= 0) {
            AgeGroup::create([
                'name' => '0-17',
                'min_age' => 0,
                'max_age' => 17
            ]);
            AgeGroup::create([
                'name' => '18-35',
                'min_age' => 18,
                'max_age' => 35
            ]);
            AgeGroup::create([
                'name' => '36-55',
                'min_age' => 36,
                'max_age' => 55
            ]);
            AgeGroup::create([
                'name' => '55+',
                'min_age' => 56,
                'max_age' => 100
            ]);
            
        }
    }
}
