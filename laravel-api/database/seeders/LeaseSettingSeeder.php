<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/29/2021
 * Time: 10:58 AM
 */

namespace Database\Seeders;

use App\Models\LeaseSetting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaseSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('lease_settings')->delete();

        LeaseSetting::create([
            'lease_number_prefix'=> 'LS',
            'invoice_number_prefix'=> 'INV',
            'invoice_footer'=> 'xxxx footer',
            'invoice_terms'=> 'yyy terms',
            'generate_invoice_on' => 25,
            'show_payment_method_on_invoice'=> true,
            'next_period_billing'=> true,
            'skip_starting_period' => false,
            'waive_penalty' => false,
        ]);
    }
}
