<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Voir formulaire', 'slug' => 'view_form'],
            ['name' => 'Répondre formulaire', 'slug' => 'respond_form'],
            ['name' => 'Créer formulaire', 'slug' => 'create_form'],
            ['name' => 'Modifier formulaire', 'slug' => 'edit_form'],
            ['name' => 'Supprimer formulaire', 'slug' => 'delete_form'],
            ['name' => 'Exporter réponses', 'slug' => 'export_responses'],
            ['name' => 'Gérer utilisateurs', 'slug' => 'manage_users'],
            ['name' => 'Gérer rôles', 'slug' => 'manage_roles'],
            ['name' => 'Gérer groupes', 'slug' => 'manage_groups'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                ['name' => $permission['name']]
            );
        }
    }
}
