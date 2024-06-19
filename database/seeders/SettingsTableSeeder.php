<?php

namespace Database\Seeders;

use App\Enums\SettingKeys;
use Illuminate\Database\Seeder;


class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $settings = [
        SettingKeys::MAX_NOTIFICATION_PER_DAY,
        SettingKeys::MAX_NOTIFICATION_PER_MONTH,
    ];
    public function run()
    {
        foreach($this->settings as $setting){
            $exists = \App\Models\BusinessSetting::where('key', $setting)->first();
            if($exists) continue;
            \App\Models\BusinessSetting::create([
                'key' => $setting,
                'value' => 5
            ]);
        }

    }
}
