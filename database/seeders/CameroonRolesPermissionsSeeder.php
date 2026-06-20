<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CameroonRolesPermissionsSeeder extends Seeder
{
    /**
     * Exécute le seeder des rôles et permissions camerounais
     */
    public function run(): void
    {
        // Réinitialiser les rôles en cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesConfig = config('roles');

        // Créer les permissions
        foreach ($rolesConfig['permissions'] as $permissionKey => $permissionLabel) {
            Permission::firstOrCreate(
                ['name' => $permissionKey],
                ['guard_name' => 'web']
            );
        }

        // Créer les rôles et assigner les permissions
        foreach ($rolesConfig['roles'] as $roleKey => $roleConfig) {
            $role = Role::firstOrCreate(
                ['name' => $roleKey],
                ['guard_name' => 'web']
            );

            // Synchroniser les permissions pour ce rôle
            $permissions = $roleConfig['permissions'] ?? [];
            $role->syncPermissions($permissions);
        }

        \Log::info('Rôles et permissions camerounais créés avec succès');
    }
}
