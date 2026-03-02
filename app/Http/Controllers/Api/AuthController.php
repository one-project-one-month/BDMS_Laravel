<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register new user
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $roleId = Role::where('name', config('roles.user'))->value('id');

        $user = User::create([
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $roleId,
        ]);

        $token = $user->createToken('flutter')->plainTextToken;

        return $this->successResponse(
            'User registered successfully',
            [
                'user' => $user,
                'token' => $token
            ],
            201
        );
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return $this->errorResponse(
                'Your account is inactive. Please contact support.',
                403
            );
        }

        $user->tokens()->delete();

        $token = $user->createToken('flutter')->plainTextToken;

        return $this->successResponse(
            'Login successful',
            [
                'user' => $user,
                'token' => $token
            ],
            200
        );
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return $this->successResponse(
            'User profile fetched successfully',
            $request->user(),
            200
        );
    }

    /**
     * Logout current device only
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            'Logged out successfully',
            null,
            200
        );
    }

    /**
     * Logout from ALL devices
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse(
            'Logged out from all devices',
            null,
            200
        );
    }
}
