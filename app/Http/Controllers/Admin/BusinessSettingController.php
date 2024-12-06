<?php

namespace App\Http\Controllers\Admin;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $settings = BusinessSetting::all()->groupBy('type');
        return view('admin.settings.index', compact('settings'));
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->settings as $type => $typeSettings) {

                foreach ($typeSettings as $key => $value) {
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }
                    BusinessSetting::updateOrCreate(
                        [
                            'type' => $type,
                            'key' => $key,
                            'business_id' => null // For global settings
                        ],
                        [
                            'value' => $value,
                            'updated_at' => now()
                        ]
                    );
                }
            }

            DB::commit();
            return back()->with('success', 'Settings updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Business Settings Update Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update settings. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return \Illuminate\Http\Response
     */
}
