<?php

namespace App\Console\Commands;

use App\Models\Language;
use Illuminate\Console\Command;

class SeedLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:language';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds default langauges';

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
        $default = [
            [
                'name' => 'English',
                'code' => 'en'
            ],
            [
                'name' => 'Japanese',
                'code' => 'ja'
            ],
            [
                'name' => 'Korean',
                'code' => 'ko'
            ],
            [
                'name' => 'IELTS',
                'code' => 'ielts'
            ],
            [
                'name' => 'TOEFL',
                'code' => 'toefl'
            ],
            [
                'name' => 'TOEIC',
                'code' => 'toeic'
            ],
            [
                'name' => 'PTE',
                'code' => 'pte',
            ],
            [
                'name' => 'Chinese',
                'code' => 'zh'
            ],
            [
                'name' => 'Spanish',
                'code' => 'es'
            ],
            [
                'name' => 'French',
                'code' => 'fr'
            ],
            [
                'name' => 'German',
                'code' => 'de'
            ]
        ];
        $languages = Language::all();
        if ($languages->isEmpty()) {
            foreach ($default as $lang) {
                Language::create($lang);
            }
        }
    }
}
