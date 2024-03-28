<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Response;
use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Http\Requests\StoreUserRequest;
use App\Models\Country;
use App\Models\LogTypes;
use App\Models\Process;
use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Request;

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

        $validated = collect($request->validated());
        $user = User::create($validated->except(['address', 'role', 'departmentids'])->toArray());
        $address = $validated->only('address')->first();

        $user->address()->create($address);
        $user->assignRole($validated->only('role')->first());
        $user->departments()->sync($validated->only('departmentids')->first());

        //todo: maybe sending email to the user with the password(this can be dispatched after redirecting check that)

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
        $departmentIds = $user->departments()->pluck('department_id')->toArray();
        $departments = Department::where(function ($query) use ($departmentIds) {
            $query->active();
            $query->orWhereIn('id', $departmentIds);
        })->get();
        return view('modules.users.createOrEdit', [
            'user' => $user,
            'roles' => Role::get(),
            'userRole' => collect($user->getRoleNames())->first(),
            'departments' => $departments,
            'departmentIds' => $departmentIds,
            'countries' => config('app.countries')
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

        $validated = collect($request->validated());

        if (!$validated->has('image')) {
            $validated->put('image', $user->image);
        }
        $user->update($validated->except('address', 'role', 'departmentids')->toArray());
        $user->address()->update($validated->only('address')->first());
        $user->assignRole($validated->only('role')->first());
        $user->departments()->sync($validated->only('departmentids')->first());


        return redirect()->route('users.index')->with('success', __('User updated successfully'));
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
                'message' => trans('Sorry, you cannot delete yourself.')
            ], 400);
        }
        $product = Product::where('user_id', $user->id)->first();
        $process = Process::independent()->where('user_id', $user->id)->first();
        if ($product || $process) {
            return response()->json([
                'message' => trans('Sorry, the user cannot be deleted because product or process exists.')
            ], 400);
        }
        $user->delete();
        $user->address()->delete();
        $user->roles()->detach();
        return response()->json([
            'message' => trans('User deleted successfully')
        ]);
    }

}