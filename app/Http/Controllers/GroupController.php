<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::withCount(['users', 'permissions'])->latest()->paginate(15);

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $users = User::all();

        return view('groups.create', compact('permissions', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $group = Group::create($request->only('name', 'description'));

        if ($request->has('permissions')) {
            $group->permissions()->sync($request->permissions);
        }

        if ($request->has('users')) {
            $group->users()->sync($request->users);
        }

        return redirect()->route('admin.groups.index')->with('success', 'Groupe créé avec succès.');
    }

    public function show(Group $group)
    {
        $group->load(['permissions', 'users']);

        return view('groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        $permissions = Permission::all();
        $users = User::all();
        $group->load(['permissions', 'users']);

        return view('groups.edit', compact('group', 'permissions', 'users'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $group->update($request->only('name', 'description'));

        $group->permissions()->sync($request->input('permissions', []));
        $group->users()->sync($request->input('users', []));

        return redirect()->route('admin.groups.index')->with('success', 'Groupe mis à jour avec succès.');
    }

    public function destroy(Group $group)
    {
        $group->permissions()->detach();
        $group->users()->detach();
        $group->delete();

        return redirect()->route('admin.groups.index')->with('success', 'Groupe supprimé avec succès.');
    }
}
