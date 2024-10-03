<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DisabilityVerification; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getProfile($user_id)
    {
        $user = User::findOrFail($user_id);
    
        $responseData = ['user' => $user];
    
        if ($user->role === 'student') {
            $verification = $user->disabilityVerification;
            $responseData['isVerified'] = $verification;
        } elseif ($user->role === 'instructor') {
            $badges = $user->badges;
            $responseData['badges'] = $badges;
        }
    
        return response()->json($responseData, 200);
    }

    public function updateProfile(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user_id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->fill($request->only(['name', 'email']));

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ], 200);
    }
}