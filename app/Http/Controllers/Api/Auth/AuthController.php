<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 * name="Authentication",
 * description="API Endpoints for User Authentication"
 * )
 */
class AuthController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Post(
     * path="/api/v1/auth/register",
     * summary="Register a new user",
     * description="Create a new user account and return an access token.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"user_name","email","password"},
     * @OA\Property(property="userName", type="string", example="Bob"),
     * @OA\Property(property="email", type="string", format="email", example="bob@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="User registered successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="User registered successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="user", type="object"),
     * @OA\Property(property="token", type="string", example="1|aBcDe...")
     * )
     * )
     * ),
     * @OA\Response(response=422, description="Validation errors")
     * )
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
     * @OA\Post(
     * path="/api/v1/auth/login",
     * summary="Login user and return tokens",
     * description="Authenticate a user and receive an access token.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * description="User credentials",
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com"),
     * @OA\Property(property="password", type="string", format="password", example="password")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login successful",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Login success"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(
     * property="user",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="roleId", type="integer", example=1),
     * @OA\Property(property="userName", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com")
     * ),
     * @OA\Property(property="accessToken", type="string", example="1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456"),
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthorized - invalid password"),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=422, description="Validation errors")
     * )
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
     * @OA\Get(
     * path="/api/v1/auth/me",
     * summary="Get authenticated user profile",
     * description="Returns the profile information of the currently logged-in user.",
     * tags={"Authentication"},
     * security={ {"bearerAuth": {}} },
     * @OA\Response(
     * response=200,
     * description="User profile fetched successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="User profile fetched successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="userName", type="string", example="aungminko"),
     * @OA\Property(property="email", type="string", example="aungminko@gmail.com"),
     * @OA\Property(property="roleId", type="integer", example=2)
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
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
     * @OA\Post(
     * path="/api/v1/auth/logout",
     * summary="Logout current device only",
     * description="Revoke the current access token used by the authenticated user.",
     * tags={"Authentication"},
     * security={ {"bearerAuth": {}} },
     * @OA\Response(
     * response=200,
     * description="Logged out successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Logged out successfully"),
     * @OA\Property(property="data", type="null", example=null)
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
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
     * @OA\Post(
     * path="/api/v1/auth/logout-all",
     * summary="Logout from ALL devices",
     * description="Revoke all access tokens for the authenticated user, logging them out from every session.",
     * tags={"Authentication"},
     * security={ {"bearerAuth": {}} },
     * @OA\Response(
     * response=200,
     * description="Logged out from all devices",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Logged out from all devices"),
     * @OA\Property(property="data", type="null", example=null)
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
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
