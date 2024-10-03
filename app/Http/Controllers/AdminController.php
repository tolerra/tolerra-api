<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisabilityVerification;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function viewDisabilityVerifications()
    {
        $verifications = DisabilityVerification::all();
        return response()->json($verifications);
    }
    
    public function viewDisabilityVerification($id)
    {
        $verification = DisabilityVerification::findOrFail($id);
        return response()->json($verification);
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