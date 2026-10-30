<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     * Authenticate user and generate API token
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with('store')->where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        // Generate Sanctum token with ability according to role
        $token = $user->createToken('api_token', [$user->role])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'store_id' => $user->store_id,
                    'store' => $user->store,
                    'phone' => $user->phone,
                    'address' => $user->address,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * POST /api/auth/register
     * Register a new customer
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'store_id' => null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        $token = $user->createToken('api_token', ['customer'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'address' => $user->address,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * POST /api/auth/logout
     * Revoke current access token
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        /** @var PersonalAccessToken|null $token */
        $token = $user?->currentAccessToken();
        $token?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.',
        ]);
    }

    /**
     * GET /api/auth/me
     * Get authenticated user profile
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->load('store');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'store_id' => $user->store_id,
                'store' => $user->store,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ]);
    }

    /**
     * POST /api/auth/google
     * Handle Google Sign-In authentication from mobile/web
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'google_id' => 'required|string',
            'name' => 'nullable|string|max:255',
            'avatar' => 'nullable|string|max:500',
        ]);

        // Find user by google_id or email
        $user = User::with('store')
            ->where('google_id', $validated['google_id'])
            ->orWhere('email', $validated['email'])
            ->first();

        if ($user) {
            $updates = [];
            if (empty($user->google_id)) {
                $updates['google_id'] = $validated['google_id'];
            }
            if (! empty($validated['avatar']) && empty($user->avatar)) {
                $updates['avatar'] = $validated['avatar'];
            }
            if (! empty($updates)) {
                $user->update($updates);
            }
        } else {
            // Create new customer account with Google identity
            $user = User::create([
                'name' => $validated['name'] ?? explode('@', $validated['email'])[0],
                'email' => $validated['email'],
                'google_id' => $validated['google_id'],
                'avatar' => $validated['avatar'] ?? null,
                'role' => 'customer',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('api_token', [$user->role])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login with Google successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->google_id,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                    'store_id' => $user->store_id,
                    'store' => $user->store,
                    'phone' => $user->phone,
                    'address' => $user->address,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }
}
