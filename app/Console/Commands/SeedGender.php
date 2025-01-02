<?php

namespace App\Console\Commands;

use App\Models\UserGender;
use Illuminate\Console\Command;

class SeedGender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:gender';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $genders = UserGender::all();
        foreach($genders as $gender) {
            $gender->delete();
        }
        if ($genders->count() == 0) {
            UserGender::create(
                [
                    'name' => 'Male',
                    'description' => ''
                ],
                
            );
            UserGender::create(
                ['name' => 'Female',
                'description' => ''
                ],
            );
        }
    }
}
