<?php

namespace App\Console\Commands;

use App\Models\Business;
use Illuminate\Console\Command;

class DeleteInactiveBusinesses extends Command
{
    protected $signature = 'businesses:cleanup';
    protected $description = 'Permanently delete businesses that have been inactive for more than a month';

    public function handle()
    {
        $monthAgo = now()->subMonth();
        
        $businesses = Business::inactive()
            ->where('deletion_scheduled_at', '<=', $monthAgo)
            ->get();

        foreach ($businesses as $business) {
            // Permanently delete the business
            $business->forceDelete();
        }

        $this->info("Deleted " . count($businesses) . " inactive businesses.");
    }
} 