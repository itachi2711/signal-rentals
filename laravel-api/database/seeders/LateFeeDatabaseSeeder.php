<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/27/2021
 * Time: 9:50 AM
 */

namespace Database\Seeders;

use App\Models\LateFee;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LateFeeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('late_fees')->delete();

        LateFee::create([
            'late_fee_name' => 'Penalty',
            'late_fee_display_name' => 'Penalty',
            'late_fee_description' => 'Penalty'
        ]);
    }
}
