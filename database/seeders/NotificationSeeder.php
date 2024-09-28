<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $users = User::limit(5)->get();

        foreach ($users as $user) {
            Notification::create([
                'event_type' => 'enrollment',
                'user_id' => $user->id,
                'msg' => 'You have a new enrollment',
                'read' => rand(0, 1),
            ]);
        }
    }
}
