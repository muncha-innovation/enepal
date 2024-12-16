<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Str;

class CreateCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default categories for news';

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
        $categories = [
            'Politics',
            'Business',
            'Technology',
            'Health',
            'Sports',
            'Entertainment',
            'Science',
            'Education',
            'Travel',
            'Fashion',
            'Food',
            'Music',
            'Movies',
            'Books',
            'Art',
            'History',
            'Religion',
            'Environment',
            'Lifestyle',
            'Culture',
            'Economy',
            'Society',
            'Weather',
            'Crime',
            'Accidents',
            'Disasters',
            'War',
            'Human Rights',
        ];
        foreach($categories as $category) {
            $existing = \App\Models\NewsCategory::where('name', $category)->first();
            if(!empty($existing)) {
                continue;
            }
            \App\Models\NewsCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'type' => 'category'
            ]);
        }
    }
}
