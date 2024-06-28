<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // create manager role
        $role = [
            'id' => 1,
            'name' => 'Manager',
            'description' => 'Data Keeper X Manager',
        ];
        DB::table("roles")->insert($role);

        // create permissions
        $permissions = [
            [
                'name' => 'Users',
                'description' => 'Manage data keeper users.',
            ],
            [
                'name' => 'Roles & Permissions',
                'description' => 'Manage Role and Permissions.',
            ],
            [
                'name' => 'Categories',
                'description' => 'Manage app users categories.',
            ],
            [
                'name' => 'Data',
                'description' => 'User data manager',
            ],
        ];
        foreach ($permissions as $key => $value) {
            DB::table('permissions')->insert($value);
        }

        // assign permissions to role
        $permission_list = DB::table('permissions')->get();
        foreach ($permission_list as $key => $value) {
            DB::table('permission_role')->insert([
                'role_id' => 1,
                'permission_id' => $value->id,
            ]);
        }

        // create manager role
        DB::table('users')->insert([
            'role_id' => 1,
            'first_name' => "admin",
            'last_name' => "admin",
            'email' => "admin@admin.com",
            'password' => Hash::make(1 . '123@admin' . 1),
            'api_secret' => Str::random(128),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('TRUNCATE TABLE roles');
        DB::statement('TRUNCATE TABLE permissions');
        DB::statement('TRUNCATE TABLE permission_role');
        DB::statement('TRUNCATE TABLE users');
    }
};
