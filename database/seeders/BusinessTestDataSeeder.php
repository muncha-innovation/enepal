<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\User;
use App\Models\Post;
use App\Models\Product;
use App\Models\Gallery;
use App\Models\Comment;
use App\Models\Like;
use App\Models\BusinessType;
use App\Models\Facility;
use App\Models\SocialNetwork;
use App\Models\Language;
use App\Models\Country;
use App\Models\AgeGroup;
use App\Models\UserGender;
use App\Models\Vendor;
use App\Models\Conversation;
use App\Models\Thread;
use App\Models\Message;

class BusinessTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test data for businesses...');

        // Create basic data first
        $this->createBasicData();
        
        // Create businesses with full relationships
        $this->createBusinessesWithRelationships();
        
        // Create businesses with posts and comments
        $this->createBusinessesWithContent();
        
        // Create businesses with conversations
        $this->createBusinessesWithConversations();

        $this->command->info('Test data created successfully!');
    }

    private function createBasicData()
    {
        $this->command->info('Creating basic data...');

        // Create business types
        BusinessType::factory()->educationConsultancy()->create();
        BusinessType::factory()->manpowerAgency()->create();
        BusinessType::factory()->travelAgency()->create();
        BusinessType::factory()->count(5)->create();

        // Create facilities
        Facility::factory()->count(10)->create();

        // Create social networks
        SocialNetwork::factory()->facebook()->create();
        SocialNetwork::factory()->instagram()->create();
        SocialNetwork::factory()->linkedin()->create();
        SocialNetwork::factory()->twitter()->create();
        SocialNetwork::factory()->youtube()->create();
        SocialNetwork::factory()->whatsapp()->create();

        // Create languages
        Language::factory()->english()->create();
        Language::factory()->nepali()->create();
        Language::factory()->hindi()->create();
        Language::factory()->chinese()->create();
        Language::factory()->japanese()->create();
        Language::factory()->korean()->create();

        // Create countries
        Country::factory()->count(10)->create();

        // Create age groups
        AgeGroup::factory()->teenagers()->create();
        AgeGroup::factory()->youngAdults()->create();
        AgeGroup::factory()->adults()->create();
        AgeGroup::factory()->middleAged()->create();
        AgeGroup::factory()->seniors()->create();

        // Create user genders
        UserGender::factory()->male()->create();
        UserGender::factory()->female()->create();
        UserGender::factory()->other()->create();
        UserGender::factory()->preferNotToSay()->create();

        // Create vendors
        Vendor::factory()->count(5)->create();

        // Create users
        User::factory()->count(20)->create();
    }

    private function createBusinessesWithRelationships()
    {
        $this->command->info('Creating businesses with relationships...');

        // Create education consultancies
        Business::factory()
            ->educationConsultancy()
            ->verified()
            ->active()
            ->count(3)
            ->create()
            ->each(function ($business) {
                $business->withRelationships();
                $business->withFacilities();
                $business->withSocialNetworks();
                $business->withLanguages();
                $business->withDestinations();
                $business->withMembers();
            });

        // Create manpower agencies
        Business::factory()
            ->manpowerAgency()
            ->verified()
            ->active()
            ->count(3)
            ->create()
            ->each(function ($business) {
                $business->withRelationships();
                $business->withFacilities();
                $business->withSocialNetworks();
                $business->withLanguages();
                $business->withDestinations();
                $business->withMembers();
            });

        // Create regular businesses
        Business::factory()
            ->active()
            ->count(5)
            ->create()
            ->each(function ($business) {
                $business->withRelationships();
                $business->withFacilities();
                $business->withSocialNetworks();
                $business->withMembers();
            });

        // Create some inactive businesses
        Business::factory()
            ->inactive()
            ->count(2)
            ->create()
            ->each(function ($business) {
                $business->withRelationships();
            });
    }

    private function createBusinessesWithContent()
    {
        $this->command->info('Creating businesses with posts, products, galleries, and comments...');

        Business::active()->get()->each(function ($business) {
            // Create posts
            $posts = Post::factory()
                ->count(rand(3, 8))
                ->create(['business_id' => $business->id]);

            // Create products
            Product::factory()
                ->count(rand(2, 6))
                ->create(['business_id' => $business->id]);

            // Create galleries
            Gallery::factory()
                ->count(rand(1, 4))
                ->create(['business_id' => $business->id]);

            // Create comments and likes for posts
            $posts->each(function ($post) {
                // Create comments
                Comment::factory()
                    ->count(rand(2, 8))
                    ->create(['post_id' => $post->id]);

                // Create some replies
                Comment::factory()
                    ->reply()
                    ->count(rand(1, 3))
                    ->create(['post_id' => $post->id]);

                // Create likes
                Like::factory()
                    ->count(rand(5, 20))
                    ->create(['post_id' => $post->id]);
            });
        });
    }

    private function createBusinessesWithConversations()
    {
        $this->command->info('Creating businesses with conversations...');

        Business::active()->limit(5)->get()->each(function ($business) {
            // Create conversations
            $conversations = Conversation::factory()
                ->count(rand(2, 5))
                ->create(['business_id' => $business->id]);

            $conversations->each(function ($conversation) {
                // Create threads
                $threads = Thread::factory()
                    ->count(rand(1, 3))
                    ->create(['conversation_id' => $conversation->id]);

                $threads->each(function ($thread) {
                    // Create messages
                    Message::factory()
                        ->count(rand(3, 10))
                        ->create(['thread_id' => $thread->id]);
                });
            });
        });
    }
} 