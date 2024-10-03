<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisabilityVerification;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function viewDisabilityVerifications()
    {
        $verifications = DisabilityVerification::with('user')->get();
    
        $verificationsWithNames = $verifications->map(function ($verification) {
            return [
                'id' => $verification->id,
                'user_id' => $verification->user_id,
                'user_name' => $verification->user->name ?? null,
                'file_path' => $verification->file_path,
                'is_verified' => $verification->is_verified,
                'created_at' => $verification->created_at, 
                'updated_at' => $verification->updated_at,
            ];
        });
    
        return response()->json([
            'verifications' => $verificationsWithNames
        ]);
    }
    
    public function viewDisabilityVerification($id)
    {
        $verification = DisabilityVerification::with('user')->findOrFail($id);
        

        $verificationWithName = [
            'id' => $verification->id,
            'user_id' => $verification->user_id,
            'user_name' => $verification->user->name ?? null,
            'file_path' => $verification->file_path,
            'is_verified' => $verification->is_verified,
            'created_at' => $verification->created_at, 
            'updated_at' => $verification->updated_at,
        ];
    
        return response()->json([
            'verification' => $verificationWithName
        ]);
    }
    

    public function updateDisabilityVerification(Request $request, $id)
    {
        $verification = DisabilityVerification::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
            'is_verified' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification->update([
            'file_path' => $request->file_path,
            'is_verified' => $request->is_verified
        ]);

        return response()->json([
            'message' => 'Disability verification updated successfully',
            'verification' => $verification
        ], 200);
    }
}