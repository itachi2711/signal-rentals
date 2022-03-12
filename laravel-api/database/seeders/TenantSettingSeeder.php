<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 8/8/2021
 * Time: 11:17 PM
 */

namespace Database\Seeders;

use App\Models\TenantSetting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TenantSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('tenant_settings')->delete();

        TenantSetting::create([
            'tenant_number_prefix' => 'TN'
        ]);
    }
}
