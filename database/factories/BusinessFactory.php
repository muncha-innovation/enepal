<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use App\Models\Address;
use App\Models\Facility;
use App\Models\SocialNetwork;
use App\Models\Language;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = BusinessType::inRandomOrder()->first() ?? BusinessType::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        
        return [
            'name' => $this->faker->company,
            'description' => [
                'en' => $this->faker->paragraph,
                'np' => $this->faker->paragraph,
            ],
            'type_id' => $type->id,
            'created_by' => $user->id,
            'contact_person_id' => $user->id,
            'is_verified' => $this->faker->boolean(20),
            'is_featured' => $this->faker->boolean(10),
            'is_active' => $this->faker->boolean(90),
            'custom_email_message' => $this->faker->optional()->sentence,
            'established_year' => $this->faker->year,
            'email' => $this->faker->unique()->companyEmail,
            'phone_1' => $this->faker->phoneNumber,
            'phone_2' => $this->faker->optional()->phoneNumber,
            'website' => $this->faker->optional()->url,
            'logo' => $this->faker->optional()->imageUrl(200, 200, 'business'),
            'cover_image' => $this->faker->optional()->imageUrl(600, 200, 'business'),
        ];
    }

    /**
     * Create a business with all relationships
     */
    public function withRelationships()
    {
        return $this->afterCreating(function (Business $business) {
            // Create address
            $business->address()->save(Address::factory()->make());
            
            // Create business hours
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                $business->hours()->create([
                    'day' => $day,
                    'is_open' => $this->faker->boolean(80),
                    'open_time' => $this->faker->time('H:i'),
                    'close_time' => $this->faker->time('H:i'),
                ]);
            }
            
            // Create business settings
            $business->settings()->createMany([
                ['type' => 'notification', 'key' => 'max_notifications_per_day', 'value' => '10'],
                ['type' => 'notification', 'key' => 'max_notifications_per_month', 'value' => '100'],
                ['type' => 'display', 'key' => 'language', 'value' => 'en'],
            ]);
            
            // Create user segments
            $business->segments()->createMany([
                ['name' => 'All Users', 'description' => 'Default segment for all users', 'type' => 'custom', 'is_default' => true, 'is_active' => true],
                ['name' => 'New Users', 'description' => 'Users who joined recently', 'type' => 'behavioral', 'is_active' => true],
                ['name' => 'Active Users', 'description' => 'Users who are frequently active', 'type' => 'behavioral', 'is_active' => true],
            ]);
        });
    }

    /**
     * Create a business with facilities
     */
    public function withFacilities()
    {
        return $this->afterCreating(function (Business $business) {
            $facilities = Facility::inRandomOrder()->limit(5)->get();
            if ($facilities->isEmpty()) {
                $facilities = Facility::factory()->count(5)->create();
            }
            
            foreach ($facilities as $facility) {
                $business->facilities()->attach($facility->id, [
                    'value' => $this->faker->randomElement(['Yes', 'No', 'Available', 'Not Available', 'Free', 'Paid'])
                ]);
            }
        });
    }

    /**
     * Create a business with social networks
     */
    public function withSocialNetworks()
    {
        return $this->afterCreating(function (Business $business) {
            $socialNetworks = SocialNetwork::inRandomOrder()->limit(3)->get();
            if ($socialNetworks->isEmpty()) {
                $socialNetworks = collect([
                    SocialNetwork::factory()->facebook()->create(),
                    SocialNetwork::factory()->instagram()->create(),
                    SocialNetwork::factory()->linkedin()->create(),
                ]);
            }
            
            foreach ($socialNetworks as $network) {
                $business->socialNetworks()->attach($network->id, [
                    'url' => $this->faker->url,
                    'is_active' => $this->faker->boolean(80),
                ]);
            }
        });
    }

    /**
     * Create a business with languages
     */
    public function withLanguages()
    {
        return $this->afterCreating(function (Business $business) {
            $languages = Language::inRandomOrder()->limit(3)->get();
            if ($languages->isEmpty()) {
                $languages = collect([
                    Language::factory()->english()->create(),
                    Language::factory()->nepali()->create(),
                    Language::factory()->hindi()->create(),
                ]);
            }
            
            foreach ($languages as $language) {
                $business->taughtLanguages()->attach($language->id, [
                    'price' => $this->faker->randomFloat(2, 50, 500),
                    'currency' => $this->faker->randomElement(['USD', 'NPR', 'EUR']),
                ]);
            }
        });
    }

    /**
     * Create a business with destinations
     */
    public function withDestinations()
    {
        return $this->afterCreating(function (Business $business) {
            $countries = Country::inRandomOrder()->limit(3)->get();
            if ($countries->isEmpty()) {
                $countries = Country::factory()->count(3)->create();
            }
            
            foreach ($countries as $country) {
                $business->destinations()->attach($country->id, [
                    'num_people_sent' => $this->faker->numberBetween(10, 1000),
                ]);
            }
        });
    }

    /**
     * Create a business with members
     */
    public function withMembers()
    {
        return $this->afterCreating(function (Business $business) {
            $users = User::inRandomOrder()->limit(5)->get();
            if ($users->isEmpty()) {
                $users = User::factory()->count(5)->create();
            }
            
            foreach ($users as $index => $user) {
                $business->members()->attach($user->id, [
                    'role' => $index === 0 ? 'owner' : $this->faker->randomElement(['admin', 'member']),
                    'is_active' => $this->faker->boolean(90),
                    'position' => $this->faker->jobTitle,
                    'department' => $this->faker->department,
                    'has_joined' => $this->faker->boolean(80),
                ]);
            }
        });
    }

    /**
     * Create a verified business
     */
    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_verified' => true,
            ];
        });
    }

    /**
     * Create a featured business
     */
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }

    /**
     * Create an active business
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Create an inactive business
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Create a business for education consultancy
     */
    public function educationConsultancy()
    {
        return $this->state(function (array $attributes) {
            $type = BusinessType::where('title', 'Education Consultancy')->first() 
                ?? BusinessType::factory()->educationConsultancy()->create();
            
            return [
                'type_id' => $type->id,
                'name' => $this->faker->company . ' Education Consultancy',
                'description' => [
                    'en' => 'Professional education consultancy services for international students',
                    'np' => 'अन्तर्राष्ट्रिय विद्यार्थीहरूको लागि व्यावसायिक शिक्षा परामर्श सेवा',
                ],
            ];
        });
    }

    /**
     * Create a business for manpower agency
     */
    public function manpowerAgency()
    {
        return $this->state(function (array $attributes) {
            $type = BusinessType::where('title', 'Manpower Agency')->first() 
                ?? BusinessType::factory()->manpowerAgency()->create();
            
            return [
                'type_id' => $type->id,
                'name' => $this->faker->company . ' Manpower Agency',
                'description' => [
                    'en' => 'Professional recruitment and placement services for overseas employment',
                    'np' => 'विदेशी रोजगारको लागि व्यावसायिक भर्ती र प्लेसमेन्ट सेवा',
                ],
            ];
        });
    }
}
