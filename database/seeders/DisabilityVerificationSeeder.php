<?php

namespace Database\Seeders;

use App\Models\DisabilityVerification;
use App\Models\User;
use Illuminate\Database\Seeder;

class DisabilityVerificationSeeder extends Seeder
{
    public function run()
    {
        $users = User::limit(5)->get();

        foreach ($users as $user) {
            DisabilityVerification::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
