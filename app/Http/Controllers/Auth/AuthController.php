<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            
            if ($user) {
                $user->load('roles');
                
                // Thêm thông tin is_admin vào response
                $userData = $user->toArray();
                $userData['is_admin'] = $this->isAdmin($user);
                
                return response()->json([
                    'message' => 'Login successful',
                    'user' => $userData
                ]);
            }

            return response()->json([
                'message' => 'Login successful',
                'user' => $user
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Handle a registration request to the application.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->whereNull('deleted_at'), // Bỏ qua soft deleted records
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Gán default role cho user mới
        $defaultRoleSettings = app(GeneralSettings::class)->default_role;
        if ($defaultRoleSettings) {
            $defaultRole = Role::where('id', $defaultRoleSettings)->first();
            if ($defaultRole) {
                $user->syncRoles([$defaultRole]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        $user->load('roles');
        
        // Thêm thông tin is_admin vào response
        $userData = $user->toArray();
        $userData['is_admin'] = $this->isAdmin($user);

        return response()->json([
            'message' => 'Registration successful',
            'user' => $userData
        ], 201);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();
        
        if ($user) {
            $user->load('roles');
            
            // Thêm thông tin is_admin vào response
            $userData = $user->toArray();
            $userData['is_admin'] = $this->isAdmin($user);
            
            return response()->json($userData);
        }
        
        return response()->json($user);
    }
    
    /**
     * Kiểm tra xem user có phải admin không
     * Admin = có role "Admin"
     */
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}

