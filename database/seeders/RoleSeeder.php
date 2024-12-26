<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            'leaveRecord.*',
            'timeRecord.*',
            'leaveType.*',
            'user.*',
            'user.create',
            'user.update',
            'user.delete',
            'admin.create',
            'admin.update',
            'admin.delete',
        ];

        // Create permissions in the database
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Super Admin with all permissions
        $superAdminRole = Role::create(['name' => 'super admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin with restricted permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminPermissions = Permission::whereNotIn('name', ['admin.create', 'admin.update', 'admin.delete'])->get();
        $adminRole->givePermissionTo($adminPermissions);

        // Standard User with only wishlist permissions
        $userRole = Role::create(['name' => 'user']);
        $userPermissions = Permission::whereIn('name', [
            'timeRecord.*', 'leaveRecord.*'
        ])->get();
        $userRole->givePermissionTo($userPermissions);
    }
}
