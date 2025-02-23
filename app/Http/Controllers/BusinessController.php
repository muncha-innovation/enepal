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

        return view('modules.business.createOrEdit', compact('businessTypes', 'countries', 'languages', 'socialNetworks'));
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

        // Handle social networks
        if (isset($data['social_networks'])) {
            $business->socialNetworks()->sync($data['social_networks']);
        }

        return redirect()->route('business.index')->with('success', 'Business Created Successfully');
    }

    /**
     * Display the specified resource.
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
    public function edit(Business $business)
    {
        //
        $businessTypes = BusinessType::all();
        $countries = Country::all();
        $languages = Language::all();
        $business->load(['address', 'settings', 'taughtLanguages', 'destinations']);
        $facilities = $business->facilities;
        $socialNetworks = \App\Models\SocialNetwork::all();
        return view('modules.business.createOrEdit', compact(['business', 'businessTypes', 'countries', 'languages', 'socialNetworks']));
    }

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
        // dd($data);
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

        // Handle social networks
        if (isset($data['social_networks'])) {
            $business->socialNetworks()->sync($data['social_networks']);
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

        $showSettings = true;
        $socialNetworks = \App\Models\SocialNetwork::all();
        return view('modules.business.createOrEdit', compact(['business', 'businessTypes', 'countries', 'showSettings','languages','socialNetworks']));
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
        return back()->with('success', 'Business Featured Successfully');
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image',
        ]);
        $path = upload('content/', 'png', $request->file('upload'));

        return response()->json(['url' => getImage($path, 'content/')]);
    }
    public function getFacilities(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:business_types,id',
        ]);
        $typeId = $request->input('type_id');
        $businessType = BusinessType::with('facilities')->findOrFail($typeId);

        return response()->json(['facilities' => $businessType->facilities]);
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
}
