<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function signup(Request $request)
    {
        // Validate user input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate API token
        $token = $user->createToken("ApiToken")->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'role'=>$user->role
        ], 201); // 201 Created
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // If user does not exist
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email or password is incorrect.',
            ], 401);
        }

        // Check if the user is banned
        if ($user->banned) { // Assuming `banned` is a boolean field (1 for banned)
            return response()->json([
                'status' => false,
                'message' => 'Your account has been blocked. Contact support for assistance.',
            ], 403);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email or password is incorrect.',
            ], 401);
        }

        // Generate token
        $token = $user->createToken("ApiToken")->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully',
            'token' => $token,
            'token_type' => 'bearer',
            'role'=>$user->role
        ], 200);
    }

    /**
     * Logout user and revoke token
     */
    public function logout(Request $request)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        // Revoke all tokens
        Auth::user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'You have successfully logged out.',
        ], 200);
    }
}
