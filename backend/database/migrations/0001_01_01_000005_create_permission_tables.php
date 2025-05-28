<?php

use Illuminate\Database\Migrations\Migration;
use \Spatie\Permission\Models\Role;
use \Spatie\Permission\Models\Permission;
use \App\Enums\Roles;
use \App\Enums\Permissions;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (Permissions::cases() as $permission) {
            Permission::create(['name' => $permission]);
        }


        foreach (Roles::cases() as $role) {
            $role = Role::create(['name' => $role]);
            foreach (Permissions::cases() as $permission) {
                $role->givePermissionTo($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (Roles::cases() as $role) {
            $role = Role::findByName($role);
            $role->delete();
        }

    }
};
