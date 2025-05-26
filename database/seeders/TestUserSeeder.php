<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Team;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->updateOrInsert(
            [ 'email' => 'organiser@example.com' ],
            [
                'name' => 'Test Organiser',
                'email' => 'organiser@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'organiser',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $user = User::where('email', 'organiser@example.com')->first();
        if ($user) {
            $hasPersonalTeam = Team::where('user_id', $user->id)
                ->where('personal_team', true)
                ->exists();
            if (! $hasPersonalTeam) {
                $team = Team::create([
                    'user_id' => $user->id,
                    'name' => $user->name . "'s Team",
                    'personal_team' => true,
                ]);
            }
        }
    }
}
