<?php

namespace Database\Seeders;

use App\Models\TenantType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TenantTypeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('tenant_types')->delete();

        TenantType::create([
            'tenant_type_name' => 'business',
            'tenant_type_display_name' => 'Business',
            'tenant_type_description' => "Business"
        ]);

        TenantType::create([
            'tenant_type_name' => 'individual',
            'tenant_type_display_name' => 'Individual',
            'tenant_type_description' => "Individual"
        ]);
    }
}
