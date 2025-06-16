<?php

namespace App\Console\Commands;

use App\Models\SocialNetwork;
use Illuminate\Console\Command;

class UpdateSocialNetworks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:update';

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
        // parent::__construct();
        // $socialNetworks = [
        //     [
        //         'name' => 'Facebook',
        //         'icon' => asset('images/socials/facebook.png'),
        //     ],
        //     [
        //         'name' => 'Instagram',
        //         'icon' => asset('images/socials/instagram.png'),
        //     ],
        //     [
        //         'name' => 'TikTok',
        //         'icon' => asset('images/socials/tiktok.png'),
        //     ],
        //     [
        //         'name' => 'X',
        //         'icon' => asset('images/socials/x.png'),
        //     ],
        //     [
        //         'name' => 'LinkedIn',
        //         'icon' => asset('images/socials/linkedin.png'),
        //     ],
        //     [
        //         'name' => 'YouTube',
        //         'icon' => asset('images/socials/youtube.png'),
        //     ],
           
        //     [
        //         'name' => 'Snapchat',
        //         'icon' => asset('images/socials/snapchat.png'),
        //     ],
        //     [
        //         'name' => 'WhatsApp',
        //         'icon' => asset('images/socials/whatsapp.png'),
        //     ],
        //     [
        //         'name' => 'Viber',
        //         'icon' => asset('images/socials/viber.png'),
        //     ]
        // ];
        // foreach ($socialNetworks as $network) {
        //     $exists = SocialNetwork::where('name', $network['name'])->exists();
        //     if ($exists) {
        //         continue;
        //     }
        //     $network = SocialNetwork::create(
        //         $network
        //     );
        // }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
