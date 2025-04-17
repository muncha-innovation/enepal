<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register routes for authenticated users (private/presence channels)
        Broadcast::routes(['middleware' => ['auth:sanctum']]);
        
        // Register routes for API authentication with Sanctum
        Broadcast::routes(['middleware' => ['auth:sanctum'], 'prefix' => 'api']);

        require base_path('routes/channels.php');
    }
}
