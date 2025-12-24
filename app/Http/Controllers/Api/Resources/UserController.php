<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Handle pagination
        $perPage = $request->get('per_page', 15);
        if ($perPage == -1) {
            $users = $query->get();
        } else {
            $users = $query->paginate($perPage);
        }

        return response()->json($users);
    }

    public function show(User $user)
    {
        $user->load('roles');
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        // Assign roles if provided
        if ($request->has('role_ids')) {
            $user->syncRoles($request->role_ids);
        }

        $user->load('roles');

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Sync roles if provided
        if ($request->has('role_ids')) {
            $user->syncRoles($request->role_ids);
        }

        $user->load('roles');

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
    * Get current authenticated user
    */
    public function me()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        return response()->json([
            'data' => $user->load('roles')
        ]);
    }

    /**
    * Update current authenticated user profile
    */
    public function updateMe(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,

            // password optional
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            $user->password = Hash::make($validated['password']);
        }

        // update name + email
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return response()->json([
            'data' => $user->fresh()->load('roles')
        ]);
    }
}


