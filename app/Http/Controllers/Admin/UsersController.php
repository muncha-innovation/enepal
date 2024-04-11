<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\Country;
use App\Models\Department;
use App\Models\Process;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
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
        $users = User::with(['address.country'])->paginate(10);
        return view('admin-views.users.index', compact(['users']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {

        abort_unless(auth()->user()->hasRole(User::SuperAdmin), Response::HTTP_FORBIDDEN);
        return view('admin-views.users.createOrEdit', [
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
            $data['profile_picture'] = upload('profile/', 'png', $request->file('image')
            );
            unset($data['image']);
        }
        $validated = collect($data);
        
        $user = User::create($validated->except(['address', 'role'])->toArray());
        $address = $validated->get('address');

        $user->address()->create($address);
        $user->assignRole($validated->get('role'));

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

        return view('admin-views.users.view', compact(['user']));
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
        $user->load(['address.country','roles']);
        return view('admin-views.users.createOrEdit', [
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
            $data['profile_picture'] = upload('profile/', 'png', $request->file('image')
            );
            unset($data['image']);
        }
        $validated = collect($data);
        $user->update($validated->except(['address', 'role'])->toArray());
        $address = $validated->get('address');
        $user->address()->update($address);
        $user->syncRoles([$validated->get('role')]);

        return redirect()->route('admin.users.index')->with('success', __('User updated successfully'));
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
        $product = Product::where('user_id', $user->id)->first();
        $process = Process::independent()->where('user_id', $user->id)->first();
        if ($product || $process) {
            return response()->json([
                'message' => trans('Sorry, the user cannot be deleted because product or process exists.'),
            ], 400);
        }
        $user->delete();
        $user->address()->delete();
        $user->roles()->detach();
        return response()->json([
            'message' => trans('User deleted successfully'),
        ]);
    }

}
