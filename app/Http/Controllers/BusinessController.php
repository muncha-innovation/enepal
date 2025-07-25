<?php

namespace App\Http\Controllers;

use App\Enums\SettingKeys;
use App\Http\Requests\StoreBusinessRequest;
use App\Models\Address;
use App\Models\Business;
use App\Models\BusinessSetting;
use App\Models\BusinessType;
use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use App\Notify\NotifyProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\Cache;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'active');
        $query = Business::with(['address.country', 'type']);

        if (!auth()->user()->hasRole('super-admin')) {
            $query->whereHas('users', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        if ($tab === 'inactive') {
            $businesses = $query->onlyTrashed()->paginate(10);
        } else {
            $businesses = $query->active()->paginate(10);
        }

        return view('modules.business.index', compact('businesses', 'tab'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $businessTypes = BusinessType::with(['facilities'])->get();
        $countries = Country::all();
        $languages = Language::all();
        $socialNetworks = \App\Models\SocialNetwork::all();
        
        // Pass default business type facilities for new business
        $defaultTypeId = $businessTypes->first()->id;
        $typeFacilities = BusinessType::with('facilities')->find($defaultTypeId)->facilities;
        
        return view('modules.business.createOrEdit', compact('businessTypes', 'countries', 'languages', 'socialNetworks', 'typeFacilities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBusinessRequest $request)
    {
        //
        $data = $request->validated();
        
        if (isset($data['address']['location'])) {
            // Convert POINT string to Point object
            preg_match('/POINT\((.*?)\)/', $data['address']['location'], $matches);
            if (isset($matches[1])) {
                list($lng, $lat) = explode(' ', $matches[1]);
                $data['address']['location'] = new Point($lat, $lng);
            }
        }

        $data['cover_image'] = upload('business/cover_image', 'png', $data['cover_image']);
        $data['logo'] = upload('business/logo', 'png', $data['logo']);
        
        $business = Business::create(
            collect($data)
                ->except(['address', 'settings', 'facilities', 'languages', 'destinations','hours','social_networks'])
                ->toArray(),
        );
        $business->setTranslation('description', 'en', $data['description']['en'])->setTranslation('description', 'np', $data['description']['np']);
        $address = new Address($data['address']);
        $business->address()->save($address);

        foreach ($data['settings'] as $key => $value) {
            BusinessSetting::create([
                'business_id' => $business->id,
                'key' => $key,
                'value' => $value,
            ]);
        }
        $business->users()->attach(auth()->user()->id, ['role' => 'owner']);
        // Handle facilities
        if (isset($data['facilities']) && is_array($data['facilities'])) {
            $facilities = [];
            foreach ($data['facilities'] as $facilityId => $value) {
                $facilities[$facilityId] = ['value' => $value];
            }
            $business->facilities()->sync($facilities);
        }

        // Only handle languages and destinations for manpower and consultancy
        $educationBusinessTypes = [5, 6]; // Adjust IDs based on your actual manpower/consultancy type IDs
        if (in_array($data['type_id'], $educationBusinessTypes)) {
            // Handle languages
            if (isset($data['languages']) && is_array($data['languages'])) {
                foreach ($data['languages'] as $language) {
                    $business->taughtLanguages()->attach($language['id'], [
                        'price' => $language['price'],
                        'currency' => $language['currency'],
                    ]);
                }
            }

            // Handle destinations
            if (isset($data['destinations']) && is_array($data['destinations'])) {
                foreach ($data['destinations'] as $destination) {
                    $business->destinations()->attach($destination['country_id'], [
                        'num_people_sent' => $destination['num_people_sent']
                    ]);
                }
            }
        }

        if ($request->has('hours')) {
            $this->updateBusinessHours($business, $request->hours);
        }

        // Handle social networks only if provided
        if (!empty($data['social_networks'])) {
            $business->socialNetworks()->sync($data['social_networks']);
        }

        return redirect()->route('business.index')->with('success', 'Business Created Successfully');
    }

    /**
     * Display the specified resource.
     * 
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function show(Business $business)
    {
        //
        return view('modules.business.show', compact('business'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function update(StoreBusinessRequest $request, Business $business)
    {
        
        $data = $request->validated();
        $this->removeCache($business->id);

        if (isset($data['address']['location'])) {
            // Convert POINT string to Point object
            preg_match('/POINT\((.*?)\)/', $data['address']['location'], $matches);
            if (isset($matches[1])) {
                list($lng, $lat) = explode(' ', $matches[1]);
                $data['address']['location'] = new Point($lat, $lng);
                
            }
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = upload('business/cover_image', 'png', $data['cover_image']);
        }
        if ($request->hasFile('logo')) {
            $data['logo'] = upload('business/logo', 'png', $data['logo']);
        }
        $business->update(
            collect($data)
                ->except(['address', 'settings', 'facilities', 'languages','destinations','hours','social_networks'])
                ->toArray(),
        );
        $address = $business->address;
        if ($address) {
            $address->update($data['address']);
        } else {
            $business->address()->save($address);
        }
        foreach ($data['settings'] as $key => $value) {
            $business->settings()->updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'value' => $value,
                ],
            );
        }

        // Handle facilities
        if (isset($data['facilities']) && is_array($data['facilities'])) {
            $facilities = [];
            foreach ($data['facilities'] as $facilityId => $value) {
                $facilities[$facilityId] = ['value' => $value];
            }
            $business->facilities()->sync($facilities);
        }

        // Only handle languages and destinations for manpower and consultancy
        $educationBusinessTypes = [5, 6]; // Adjust IDs based on your actual manpower/consultancy type IDs
        if (in_array($data['type_id'], $educationBusinessTypes)) {
            // Handle languages
            if (isset($data['languages'])) {
                $business->taughtLanguages()->detach();
                foreach ($data['languages'] as $language) {
                    $business->taughtLanguages()->attach($language['id'], [
                        'price' => $language['price'],
                        'currency' => $language['currency'],
                        
                    ]);
                }
            }

            // Handle destinations
            if (isset($data['destinations'])) {
                $business->destinations()->detach();
                foreach ($data['destinations'] as $destination) {
                    $business->destinations()->attach($destination['country_id'], [
                        'num_people_sent' => $destination['num_people_sent']
                    ]);
                }
            }
        } else {
            // If business type changed from education to non-education, remove related data
            $business->taughtLanguages()->detach();
            $business->destinations()->detach();
        }

        if ($request->has('hours')) {
            $this->updateBusinessHours($business, $request->hours);
        }

        // Handle social networks - sync will remove any networks not in the array
        if (isset($data['social_networks'])) {
            $business->socialNetworks()->sync($data['social_networks']);
        } else {
            // If no social networks provided, remove all existing ones
            $business->socialNetworks()->detach();
        }

        return back()->with('success', 'Business Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function destroy($businessId)
    {
        $business = Business::withTrashed()->findOrFail($businessId);
        abort_unless($business->created_by === auth()->id(), 403, 'You are not authorized to delete this business');
        if ($business->trashed()) {
            $location = route('business.index', ['tab' => 'inactive']);
            $business->forceDelete();
        } else {
            $location = route('business.index');
            $business->delete();
        }
        return response()->json(['location' => $location]);
    }

    public function restore($businessId)
    {
        $business = Business::withTrashed()->findOrFail($businessId);

        abort_unless($business->created_by === auth()->id(), 403, 'You are not authorized to restore this business');

        $business->restore();

        return back()->with('success', 'Business has been restored successfully.');
    }

    public function setting(Business $business)
    {
        $businessTypes = BusinessType::all();
        $countries = Country::all();
        $business->load(['address', 'settings','destinations','taughtLanguages']);
        
        $languages = Language::all();
        $typeFacilities = BusinessType::with('facilities')->find($business->type_id)->facilities;
        $showSettings = true;
        $socialNetworks = \App\Models\SocialNetwork::all();
        return view('modules.business.createOrEdit', compact(['business', 'businessTypes', 'countries', 'showSettings','languages','socialNetworks', 'typeFacilities']));
    }
    public function verify(Business $business)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        $business->update(['is_verified' => !$business->is_verified]);

        if ($business->is_verified) {
            $notify = new NotifyProcess();
            $notify->setTemplate(SettingKeys::BUSINESS_VERIFICATION_EMAIL)
                ->setUser($business)
                ->withShortCodes([
                    'business_name' => $business->name,
                    'site_name' => config('app.name'),
            ]);
            $notify->send();
        }

        return back()->with('success', 'Business Verified Successfully');
    }
    public function featured(Request $request, Business $business)
    {
        $business->update(['is_featured' => !$business->is_featured]);
        $this->removeCache($business->id);

        return back()->with('success', 'Business Featured Successfully');
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image',
        ]);
        $path = upload('content/', 'png', $request->file('upload'));
        $this->removeCache($business->id);

        return response()->json(['url' => getImage($path, 'content/')]);
    }
  

    public function getLanguageRow($index)
    {
        return view('modules.business.components.language_row', [
            'index' => $index,
            'languages' => \App\Models\Language::all(),
        ])->render();
    }

    public function getDestinationRow($index)
    {
        return view('modules.business.components.destination_row', [
            'index' => $index,
            'countries' => \App\Models\Country::all(),
        ])->render();
    }

    private function updateBusinessHours($business, $hoursData)
    {
        if (!$hoursData) return;
        $this->removeCache($business->id);

        // First, mark all existing hours as closed
        $business->hours()->update(['is_open' => false]);

        // Then update or create new hours
        foreach ($hoursData as $day => $schedule) {
            if (!isset($schedule['is_open']) || !$schedule['is_open']) {
                continue;
            }
            
            $business->hours()->updateOrCreate(
                ['day' => $day],
                [
                    'is_open' => true,
                    'open_time' => $schedule['open_time'],
                    'close_time' => $schedule['close_time'],
                ]
            );
        }
    }

    /**
     * Save or update general business information
     */
    public function saveGeneral(StoreBusinessRequest $request, Business $business = null)
    {
        $data = $request->validated();
        
        // For new business creation
        if (!$business) {
            $business = Business::create([
                'name' => $data['name'],
                'type_id' => $data['type_id'],
                'created_by' => auth()->id(),
            ]);
            
            // Attach owner automatically
            $business->users()->attach(auth()->id(), ['role' => 'owner']);
            
            // Add default settings
            $defaultSettings = [
                SettingKeys::MAX_NOTIFICATION_PER_DAY => 0,
                SettingKeys::MAX_NOTIFICATION_PER_MONTH => 0,
            ];
            
            foreach ($defaultSettings as $key => $value) {
                BusinessSetting::create([
                    'business_id' => $business->id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
            
            // Handle description translations if present
            if (isset($data['description'])) {
                foreach ($data['description'] as $locale => $text) {
                    $business->setTranslation('description', $locale, $text);
                }
                $business->save();
            }
            
            // Handle file uploads
            if ($request->hasFile('logo')) {
                $business->logo = upload('business/logo', 'png', $request->file('logo'));
                $business->save();
            }
            
            if ($request->hasFile('cover_image')) {
                $business->cover_image = upload('business/cover_image', 'png', $request->file('cover_image'));
                $business->save();
            }
            
            // Handle other fields
            $business->established_year = $data['established_year'] ?? null;
            $business->custom_email_message = $data['custom_email_message'] ?? null;
            $business->is_active = $data['is_active'] ?? true;
            $business->save();
            
            // Check if Ajax request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Business created successfully!',
                    'business_id' => $business->id,
                    'redirect' => route('business.setting', $business) . '#details'
                ]);
            }
            
            // Success message for non-Ajax request
            return redirect()->route('business.setting', $business)->with('success', 'Business created successfully! Please complete the other sections.');
        } 
        // For existing business update
        else {
            $updates = [
                'name' => $data['name'],
                'type_id' => $data['type_id'],
                'established_year' => $data['established_year'] ?? null,
                'custom_email_message' => $data['custom_email_message'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ];
            
            $business->update($updates);
            
            // Handle description translations if present
            if (isset($data['description'])) {
                foreach ($data['description'] as $locale => $text) {
                    $business->setTranslation('description', $locale, $text);
                }
                $business->save();
            }
            
            // Handle file uploads
            if ($request->hasFile('logo')) {
                $business->logo = upload('business/logo', 'png', $request->file('logo'));
                $business->save();
            }
            
            if ($request->hasFile('cover_image')) {
                $business->cover_image = upload('business/cover_image', 'png', $request->file('cover_image'));
                $business->save();
            }
            
            // Check if Ajax request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'General information updated successfully!'
                ]);
            }
            
            return back()->with('success', 'General information updated successfully!');
        }
    }
    
    /**
     * Save or update business details
     */
    public function saveDetails(StoreBusinessRequest $request, Business $business)
    {
        $data = $request->validated();
        $updates = [];
        $this->removeCache($business->id);
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $updates['logo'] = upload('business/logo', 'png', $request->file('logo'));
        }
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $updates['cover_image'] = upload('business/cover_image', 'png', $request->file('cover_image'));
        }
        
        // Update the business record
        if (!empty($updates)) {
            $business->update($updates);
        }
        
        // Handle facilities
        if (isset($data['facilities']) && is_array($data['facilities'])) {
            $facilities = [];
            foreach ($data['facilities'] as $facilityId => $value) {
                $facilities[$facilityId] = ['value' => $value];
            }
            $business->facilities()->sync($facilities);
        }
        
        // Handle business hours
        if ($request->has('hours')) {
            $this->updateBusinessHours($business, $request->hours);
        }
        
        // Return JSON response for Ajax requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Business details updated successfully!'
            ]);
        }
        
        return back()->with('success', 'Business details updated successfully!');
    }
    
    /**
     * Save or update business address
     */
    public function saveAddress(StoreBusinessRequest $request, Business $business)
    {
        $data = $request->validated();
        $this->removeCache($business->id);
        
        // Process location data
        if (isset($data['address']['location'])) {
            preg_match('/POINT\((.*?)\)/', $data['address']['location'], $matches);
            if (isset($matches[1])) {
                list($lng, $lat) = explode(' ', $matches[1]);
                $data['address']['location'] = new Point($lat, $lng);
            }
        }
        
        // Update basic contact information
        $business->update([
            'email' => $data['email'] ?? $business->email,
            'phone_1' => $data['phone_1'] ?? $business->phone_1,
            'phone_2' => $data['phone_2'] ?? null,
            'contact_person_name' => $data['contact_person_name']??null,
        ]);
        
        // Update or create address
        if ($business->address) {
            $business->address->update($data['address']);
        } else {
            $business->address()->create($data['address']);
        }
        
        // Return JSON response for Ajax requests
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Address and contact information updated successfully!'
            ]);
        }
        
        return back()->with('success', 'Address and contact information updated successfully!');
    }
    
    /**
     * Save or update business contact information
     */
    public function saveContact(StoreBusinessRequest $request, Business $business)
    {
        $data = $request->validated();
        $this->removeCache($business->id);

        // Update basic contact information
        $business->update([
            'email' => $data['email'],
            'phone_1' => $data['phone_1'],

            'phone_2' => $data['phone_2'] ?? null,
            'contact_person_name' => $data['contact_person_name']??null,

            'established_year' => $data['established_year'] ?? null,
            'custom_email_message' => $data['custom_email_message'] ?? null,
            'is_active' => $data['is_active'],
        ]);
        
        // Update settings if provided
        if (isset($data['settings'])) {
            foreach ($data['settings'] as $key => $value) {
                $business->settings()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }
        
        // Handle languages (for education business types)
        $educationBusinessTypes = [5, 6]; // Adjust IDs based on your manpower/consultancy types
        if (in_array($business->type_id, $educationBusinessTypes)) {
            if (isset($data['languages'])) {
                $business->taughtLanguages()->detach();
                foreach ($data['languages'] as $language) {
                    $business->taughtLanguages()->attach($language['id'], [
                        'price' => $language['price'],
                        'currency' => $language['currency'],
                    ]);
                }
            }
            
            // Handle destinations
            if (isset($data['destinations'])) {
                $business->destinations()->detach();
                foreach ($data['destinations'] as $destination) {
                    $business->destinations()->attach($destination['country_id'], [
                        'num_people_sent' => $destination['num_people_sent']
                    ]);
                }
            }
        }
        
        // Handle social networks
        if (isset($data['social_networks'])) {
            $business->socialNetworks()->sync($data['social_networks']);
        }
        
        return back()->with('success', 'Contact information updated successfully!');
    }

    /**
     * Save or update business social media information
     */
    public function saveSocialMedia(StoreBusinessRequest $request, Business $business)
    {
        $data = $request->validated();
        $this->removeCache($business->id);
        // Process social networks data
        if (isset($data['social_networks'])) {
            $socialNetworks = collect($data['social_networks'])
                ;
            $business->socialNetworks()->sync($socialNetworks);
        } else {
            $business->socialNetworks()->detach();
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Social media information updated successfully!',
                'redirect' => route('business.setting', $business)
            ]);
        }
        
        return back()->with('success', 'Social media information updated successfully!');
    }
    
    /**
     * Save or update business manpower/consultancy information
     */
    public function saveManpowerConsultancy(StoreBusinessRequest $request, Business $business)
    {
        $data = $request->validated();
        $business->destinations()->detach();
        $business->taughtLanguages()->detach();

        if (isset($data['languages'])) {
            foreach ($data['languages'] as $language) {
                if (!isset($language['id']) || empty($language['id'])) continue;
                
                $business->taughtLanguages()->attach($language['id'], [
                    'currency' => $language['currency'],
                    'price' => $language['price'] ?? null,
                ]);
            }
        }
        
        // Handle destinations
        if (isset($data['destinations'])) {
            foreach ($data['destinations'] as $destination) {
                if (!isset($destination['country_id']) || empty($destination['country_id'])) continue;
                
                $business->destinations()->attach($destination['country_id'], [
                    'num_people_sent' => $destination['num_people_sent'] ?? null,
                ]);
            }
        }
        $this->removeCache($business->id);
        // Return JSON response for Ajax requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Manpower/consultancy information updated successfully!'
            ]);
        }
        
        return back()->with('success', 'Manpower/consultancy information updated successfully!');
    }

    /**
     * Display the owner profile for a business
     * 
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function ownerProfile(Business $business)
    {
        // Only super admins can view the owner profile
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        
        // Get the owner of the business
        $owner = $business->users()
            ->wherePivot('role', 'owner')
            ->first();
        
        return view('modules.business.owner-profile', compact('business', 'owner'));
    }

    private function removeCache($id) {
        $cacheKey = "business:full:{$id}";
        Cache::forget($cacheKey);
        if(!app()->environment('local')) {
            Cache::tags(['businesses'])->flush();
        }
    }
}
