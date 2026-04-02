<?php

namespace App\Http\Controllers\Web\Admin;

use App\DataTables\UserDataTable;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\User\StoreUserRequest;
use App\Http\Requests\Web\Admin\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userTypes = auth()->user()->isManager()
            ? [UserType::Employee => UserType::getDescription(UserType::Employee)]
            : UserType::asSelectArray();

        $employeeRoles = Role::whereIn('name', ['cashier', 'cook', 'chef', 'server', 'barista'])
            ->pluck('name', 'name')
            ->map(fn ($name) => ucfirst($name))
            ->toArray();

        return view('admin.user.create', [
            'userTypes' => $userTypes,
            'employeeRoles' => $employeeRoles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // Managers can only create Employees
        if (auth()->user()->isManager() && $request->user_type != UserType::Employee) {
            abort(403, 'Managers can only create Employee accounts.');
        }

        $user = User::create($request->validated());

        if ($request->user_type == UserType::Employee && $request->filled('employee_role')) {
            $user->assignRole($request->employee_role);
        }

        alert()->success('User has been added successfully');
        return redirect()->route('admin.user.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.user.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Managers cannot edit Admins or other Managers
        if (auth()->user()->isManager() && !$user->isEmployee()) {
            abort(403, 'Managers can only edit Employee accounts.');
        }

        $userTypes = auth()->user()->isManager()
            ? [UserType::Employee => UserType::getDescription(UserType::Employee)]
            : UserType::asSelectArray();

        $employeeRoles = Role::whereIn('name', ['cashier', 'cook', 'chef', 'server', 'barista'])
            ->pluck('name', 'name')
            ->map(fn ($name) => ucfirst($name))
            ->toArray();

        return view('admin.user.edit', [
            'user' => $user,
            'userTypes' => $userTypes,
            'employeeRoles' => $employeeRoles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->filled('password')) {
            $user->update(['password' => $request->password]);
        }

        $user->update($request->except(['password', 'employee_role']));

        if ($request->user_type == UserType::Employee && $request->filled('employee_role')) {
            $user->syncRoles($request->employee_role);
        } elseif ($request->user_type != UserType::Employee) {
            $user->syncRoles([]);
        }

        alert()->success('User has been updated successfully');
        return redirect()->route('admin.user.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->user()->isManager()) {
            abort(403, 'Managers are not allowed to delete users.');
        }

        $user->delete();

        alert()->success('User has been deleted successfully');
        return redirect()->route('admin.user.index');
    }
}
