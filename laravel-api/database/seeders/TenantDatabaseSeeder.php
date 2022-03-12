<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('tenants')->delete();

        Tenant::create([
            'email' => 'tenant@tenant.com',
            'first_name' => 'Tenant',
            'last_name' => 'Tenant',
            'phone' => '254724475357',
            'password' => 'tenant',
        ]);
    }
}
