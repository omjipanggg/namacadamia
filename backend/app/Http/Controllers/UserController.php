<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = $this->paginate($request, User::query()->with('branches'), ['name', 'email', 'id']);
        return UserResource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:191',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:191',
            'is_active' => 'nullable|boolean',
            'branches' => 'nullable|array',
            'branches.*' => 'integer|distinct|exists:branches,id',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|distinct|exists:roles,id'
        ]);
        
        $validated['email_verified_at'] = now();
        $validated['remember_token'] = Str::random(10);
        
        $user = User::create($validated);

        $user->assignRole($validated['roles']);
        $user->branches()->attach($validated['branches']);
        
        return $this->sendResponse(true, 'User created', $user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $user->syncRoles($request->roles);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        return $this->sendResponse($user->delete(), 'User deleted');
    }

    public function bulkReset(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        $users = User::whereIn('id', $validated['ids'])->get();

        foreach ($users as $user) {
            $user->password = Hash::make('namada123');
            $user->save();
        }

        return $this->sendResponse(true, 'Password has been resetted');
    }

    public function bulkToggleActive(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        $users = User::whereIn('id', $validated['ids'])->get();

        foreach ($users as $user) {
            $user->is_active = !$user->is_active;
            $user->save();
        }

        return $this->sendResponse(true, 'Users have been deactivated');
    }
}
