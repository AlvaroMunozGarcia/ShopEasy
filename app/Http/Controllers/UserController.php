<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $users = User::with('roles')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id');
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $roleIds = $request->input('roles');
        $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        $user->assignRole($roleNames);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'id');
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
         $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ]);

        $input = $request->except(['password', 'password_confirmation', 'roles']);

        if (!empty($request->password)) {
            $input['password'] = Hash::make($request->password);
        }

        $user->update($input);

        $roleIds = $request->input('roles');
        $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        $user->syncRoles($roleNames);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
             return redirect()->route('admin.users.index')
                         ->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
                         ->with('success', 'Usuario archivado exitosamente.');
    }
}
