<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPermissions = Permission::all();

        // Admin role - all permissions
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin']
        );
        $admin->permissions()->sync($allPermissions->pluck('id'));

        // Moderator role - view_form, respond_form, create_form, edit_form, export_responses
        $moderator = Role::firstOrCreate(
            ['slug' => 'moderator'],
            ['name' => 'Moderator']
        );
        $moderatorPermissions = $allPermissions->whereIn('slug', [
            'view_form',
            'respond_form',
            'create_form',
            'edit_form',
            'export_responses',
        ]);
        $moderator->permissions()->sync($moderatorPermissions->pluck('id'));

        // Citizen role - view_form, respond_form
        $citizen = Role::firstOrCreate(
            ['slug' => 'citizen'],
            ['name' => 'Citizen']
        );
        $citizenPermissions = $allPermissions->whereIn('slug', [
            'view_form',
            'respond_form',
        ]);
        $citizen->permissions()->sync($citizenPermissions->pluck('id'));
    }
}
