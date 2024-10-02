<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DisabilityVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    protected $cloudinaryController;

    public function __construct(CloudinaryController $cloudinaryController)
    {
        $this->cloudinaryController = $cloudinaryController;
    }

    public function register(Request $request, $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['student', 'instructor'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($role !== $request->role) {
            return response()->json(['message' => 'Invalid role'], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        if ($role === 'student') {
            $validator = Validator::make($request->all(), [
                'disability_card' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                $user->delete(); // rollback user creation
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $uploadedFileUrl = $this->cloudinaryController->upload($request, 'disability_card', 'disability_cards');

            DisabilityVerification::create([
                'user_id' => $user->id,
                'card_path' => $uploadedFileUrl,
            ]);

            return response()->json([
                'message' => 'Registration successful. Please wait for admin verification.',
                'user' => $user
            ], 201);
        }

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user
        ], 201);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ] 
        ], 200);
    }
}