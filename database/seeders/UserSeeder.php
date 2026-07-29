<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@citizenforms.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // Moderator user
        $moderator = User::firstOrCreate(
            ['email' => 'moderateur@citizenforms.local'],
            [
                'name' => 'Moderateur',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $moderatorRole = Role::where('slug', 'moderator')->first();
        if ($moderatorRole) {
            $moderator->roles()->syncWithoutDetaching([$moderatorRole->id]);
        }

        // Citizen user
        $citizen = User::firstOrCreate(
            ['email' => 'citoyen@citizenforms.local'],
            [
                'name' => 'Citoyen',
                'password' => Hash::make('password'),
                'user_type' => 'citizen',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $citizenRole = Role::where('slug', 'citizen')->first();
        if ($citizenRole) {
            $citizen->roles()->syncWithoutDetaching([$citizenRole->id]);
        }
    }
}
