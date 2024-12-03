<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\Facility;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();

        Schema::defaultStringLength(191);
        Validator::extend('valid_facility_value', function ($attribute, $value, $parameters, $validator) {
            
            $facilityId = explode('.', $attribute)[1];
            $facility = Facility::find($facilityId);
    
            if (!$facility) {
                return false; // Facility ID is invalid
            }
            if (is_string($value)) {
                return strlen($value) <= 255;
            } elseif (is_numeric($value)) {
                return $value >= 0;
            }
    
            return false;
        });
    }
}
