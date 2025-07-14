<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;

class ExampleUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating example business data...');

        // Example 1: Create a simple business
        $simpleBusiness = Business::factory()->create();
        $this->command->info("Created simple business: {$simpleBusiness->name}");

        // Example 2: Create a business with all relationships
        $fullBusiness = Business::factory()
            ->withRelationships()
            ->withFacilities()
            ->withSocialNetworks()
            ->withLanguages()
            ->withDestinations()
            ->withMembers()
            ->create();
        $this->command->info("Created full business: {$fullBusiness->name}");

        // Example 3: Create an education consultancy
        $educationBusiness = Business::factory()
            ->educationConsultancy()
            ->verified()
            ->active()
            ->withRelationships()
            ->withLanguages()
            ->withDestinations()
            ->create();
        $this->command->info("Created education consultancy: {$educationBusiness->name}");

        // Example 4: Create a manpower agency
        $manpowerBusiness = Business::factory()
            ->manpowerAgency()
            ->verified()
            ->active()
            ->withRelationships()
            ->withDestinations()
            ->create();
        $this->command->info("Created manpower agency: {$manpowerBusiness->name}");

        // Example 5: Create businesses with posts and engagement
        $businessesWithContent = Business::factory()
            ->active()
            ->count(3)
            ->create()
            ->each(function ($business) {
                // Create posts
                $posts = Post::factory()
                    ->count(rand(2, 5))
                    ->create(['business_id' => $business->id]);

                // Create comments and likes for each post
                $posts->each(function ($post) {
                    Comment::factory()
                        ->count(rand(3, 8))
                        ->create(['post_id' => $post->id]);

                    Like::factory()
                        ->count(rand(5, 15))
                        ->create(['post_id' => $post->id]);
                });
            });
        $this->command->info("Created 3 businesses with posts and engagement");

        // Example 6: Create some inactive businesses
        $inactiveBusinesses = Business::factory()
            ->inactive()
            ->count(2)
            ->create();
        $this->command->info("Created 2 inactive businesses");

        // Example 7: Create featured businesses
        $featuredBusinesses = Business::factory()
            ->featured()
            ->active()
            ->count(2)
            ->create();
        $this->command->info("Created 2 featured businesses");

        $this->command->info('Example data created successfully!');
        
        // Show summary
        $this->showSummary();
    }

    private function showSummary()
    {
        $this->command->info("\n=== SUMMARY ===");
        $this->command->info("Total businesses: " . Business::count());
        $this->command->info("Active businesses: " . Business::active()->count());
        $this->command->info("Inactive businesses: " . Business::where('is_active', false)->count());
        $this->command->info("Verified businesses: " . Business::verified()->count());
        $this->command->info("Featured businesses: " . Business::where('is_featured', true)->count());
        $this->command->info("Total posts: " . Post::count());
        $this->command->info("Total comments: " . Comment::count());
        $this->command->info("Total likes: " . Like::count());
        
        // Show business types
        $this->command->info("\nBusiness types:");
        Business::with('type')->get()->groupBy('type.title')->each(function ($businesses, $type) {
            $this->command->info("  {$type}: " . $businesses->count());
        });
    }
} 