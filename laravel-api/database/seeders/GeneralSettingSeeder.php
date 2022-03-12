<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/6/2021
 * Time: 8:44 AM
 */

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSettingSeeder extends Seeder
{
    public function run()
    {
         DB::table('general_settings')->delete();

        GeneralSetting::create([
            'company_name' => 'Signal Rentals Realtors LTD',
            'company_type' => 'Real Estate',
            'email' => "devtest@devtest.com",
            'phone' => "+254724475357",
            'currency' => 'USD',
            'physical_address'=> '3rd Floor, Juhudi Plaza, SafariWalk off Thika Highway',
            'postal_address'=> 'P.O Box 81283 - 90299, Nairobi, Kenya',
            'website_url'=> 'www.robisignals.com',
            'postal_code'=> '90299',
            'date_format' => "d-m-Y",
            'amount_thousand_separator' => ",",
            'amount_decimal_separator' => ".",
            'amount_decimal' => "2",
        ]);
    }

}
