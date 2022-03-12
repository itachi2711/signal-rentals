<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{

    public function run()
    {

        DB::table('roles')->delete();

        $admin = Role::create([
            'name' => 'Admin',
            'display_name' => 'Admin',
            'description' => "site admin"
        ]);

        DB::table('permission_role')->delete();

        $permissions = Permission::select('id')->get();

        if (!is_null($admin) && (!is_null($permissions))){
            $admin->permissions()->sync($permissions);
        }

    }

}
