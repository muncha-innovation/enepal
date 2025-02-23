<?php

namespace Database\Seeders;

use App\Models\SocialNetwork;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $medias = [
            [
                'name' => 'Facebook', 
                'icon' => asset('images/socials/facebook.png'),
            ],
            [
                'name' => 'Instagram',
                'icon' => asset('images/socials/instagram.png'),
            ]
        ];
        foreach ($medias as $media) {
            $media = SocialNetwork::firstOrCreate(
                $media
            );
        }
    }
}
