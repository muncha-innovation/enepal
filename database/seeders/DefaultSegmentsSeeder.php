<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\UserSegment;
use Illuminate\Database\Seeder;

class DefaultSegmentsSeeder extends Seeder
{
    public function run()
    {
        // Get all businesses
        $businesses = Business::all();

        foreach ($businesses as $business) {
            // Create admin segment
            $adminSegment = UserSegment::firstOrCreate([
                'business_id' => $business->id,
                'type' => 'admin',
                'is_default' => true
            ], [
                'name' => 'Administrators',
                'description' => 'Business administrators',
                'is_active' => true
            ]);

            // Create member segment
            $memberSegment = UserSegment::firstOrCreate([
                'business_id' => $business->id,
                'type' => 'member',
                'is_default' => true
            ], [
                'name' => 'Members',
                'description' => 'Regular business members',
                'is_active' => true
            ]);

            // Add users to their respective segments based on their roles
            $business->users()->each(function ($user) use ($adminSegment, $memberSegment) {
                $role = $user->pivot->role;
                if ($role === 'admin' || $role === 'owner') {
                    $adminSegment->users()->syncWithoutDetaching([$user->id]);
                } else {
                    $memberSegment->users()->syncWithoutDetaching([$user->id]);
                }
            });
        }
    }
}
