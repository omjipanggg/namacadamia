<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    public function index(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        return RoleResource::collection($roles);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return RoleResource::make($role);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:191|unique:roles,name',
        ]);

        $role = Role::create($validatedData);

        return RoleResource::make($role);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:191|unique:roles,name,' . $role->id,
        ]);

        $role->update($validatedData);

        return RoleResource::make($role);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted']);
    }   

    public function bulkDestroy(Request $request)
    {
        $validatedData = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:roles,id',
        ]);

        Role::whereIn('id', $validatedData['ids'])->delete();

        return response()->json(['message' => 'Roles deleted']);
    }
}
