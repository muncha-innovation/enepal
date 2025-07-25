<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SettingKeys;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\Country;
use App\Models\User;
use App\Notify\NotifyProcess;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'active');
        if($tab=='inactive') {
            $users = 
        $users = User::inactive()->with(['addresses.country','workExperience','education'])->paginate(10);
        } else {

    $users = User::active()->with(['addresses.country','workExperience','education'])->paginate(10);
        }
        return view('admin.users.index', compact(['users', 'tab']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {

        abort_unless(auth()->user()->hasRole(User::SuperAdmin), Response::HTTP_FORBIDDEN);
        return view('admin.users.createOrEdit', [
            'roles' => Role::get(),
            'countries' => Country::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole(User::SuperAdmin), Response::HTTP_FORBIDDEN);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['profile_picture'] = upload(
                'profile/',
                'png',
                $request->file('image')
            );
            unset($data['image']);
        }
        
        // Remove nested arrays and relationship data before creating user
        $userData = collect($data)->except(['address', 'role', 'original_password', 'education', 'experience', 'preferences'])->toArray();
        $user = User::create($userData);
        
        // Handle relationships
        if (isset($data['address'])) {
            $user->addresses()->create($data['address']);
        }

        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        // Create related records
        if (isset($data['education'])) {
            foreach ($data['education'] as $education) {
                $user->education()->create($education);
            }
        }

        if (isset($data['experience'])) {
            foreach ($data['experience'] as $experience) {
                $user->workExperience()->create($experience);
            }
        }

        if (isset($data['preferences'])) {
            $preferences = $data['preferences'];
            $preferences['user_id'] = $user->id;
            $user->preference()->create($preferences);
        }

        // Send notification
        $notify = new NotifyProcess();
        $notify->setTemplate(SettingKeys::WELCOME_EMAIL)
            ->setUser($user)
            ->withShortCodes([
                'site_name' => config('app.name'),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => $data['original_password'] ?? null,
            ]);
        $notify->send();

        return redirect()->route('admin.users.index')->with('success', __('User created successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        $user->load(['preference']);
        // dd($user->preference->countries);

        return view('admin.users.view', compact(['user']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return Application|Factory|View
     */
    public function edit(User $user): View
    {
        abort_unless(auth()->user()->hasRole(User::SuperAdmin), Response::HTTP_FORBIDDEN);
        $user->load(['addresses.country', 'roles', 'education', 'workExperience', 'preference']);
        return view('admin.users.createOrEdit', [
            'user' => $user,
            'roles' => Role::get(),
            'userRole' => collect($user->getRoleNames())->first(),
            'countries' => Country::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(StoreUserRequest $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole(User::SuperAdmin), Response::HTTP_FORBIDDEN);

        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $data['profile_picture'] = upload(
                'profile/',
                'png',
                $request->file('image')
            );
            unset($data['image']);
        }

        // Remove nested arrays and relationship data before updating user
        $userData = collect($data)->except(['address', 'role', 'original_password', 'education', 'experience', 'preferences'])->toArray();
        $user->update($userData);
        // Handle relationships
        if (isset($data['address'])) {
            $user->addresses()->update($data['address']);
        }

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        // Update education records
        if (isset($data['education'])) {
            $user->education()->delete();
            foreach ($data['education'] as $education) {
                $user->education()->create($education);
            }
        }

        // Update work experience records
        if (isset($data['experience'])) {
            $user->workExperience()->delete();
            foreach ($data['experience'] as $experience) {
                $user->workExperience()->create($experience);
            }
        }

        // Update preferences
        if (isset($data['preferences'])) {
            $preferences = $data['preferences'];
            
            // Debug: Log the preferences data
            Log::info('User preferences update', [
                'user_id' => $user->id,
                'preferences' => $preferences,
                'countries' => $preferences['countries'] ?? null
            ]);
            
            if ($user->preference) {
                // Update existing preference
                $user->preference->update($preferences);
            } else {
                // Create new preference
                $preferences['user_id'] = $user->id;
                $user->preference()->create($preferences);
            }
        }

        return redirect()->route('admin.users.edit', $user)->with('success', __('User updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Void
     */
    public function destroy(User $user)
    {
        abort_unless(auth()->user()->hasRole(User::SuperAdmin), Response::HTTP_FORBIDDEN);
        
        if ($user->id == auth()->user()->id) {
            return response()->json([
                'message' => trans('Sorry, you cannot delete yourself.'),
            ], 400);
        }
        
        // Check if user is inactive - only inactive users can be deleted
        if ($user->is_active) {
            return back()->with('error', __('Only inactive users can be deleted. Please deactivate the user first.'));
        }
        
        $user->delete();
        $user->addresses()->delete();
        $user->roles()->detach();
        return back()->with('success', __('User deleted successfully'));
    }

    /**
     * Generate a random password for the user and send it via email.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->user()->hasRole(User::SuperAdmin), Response::HTTP_FORBIDDEN);
        
        // Generate a random password (8 characters)
        $password = Str::random(8);
        
        // Update the user's password
        $user->password = Hash::make($password);
        $user->force_update_password = true;
        $user->last_password_updated = now();
        $user->save();
        
        try{
            Mail::send('mail.temporary_password', [
                'user' => $user,
                'password' => $password,
            ], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject(__('Password Reset'));
            });
        }catch(Exception $e) {
            dd($e);
        }
        
        return redirect()->back()->with('success', __('Password has been reset and emailed to the user'));
    }
}
