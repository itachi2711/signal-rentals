<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:33 PM
 */

namespace Database\Seeders;

use App\Models\EmailConfigSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailSettingTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('email_config_settings')->delete();

        EmailConfigSetting::create([
            'protocol' => 'smtp',
            'smpt_host' => 'sendmail.gmail.com',
            'smpt_username' => 'gmasdfa@gmail.com',
            'smpt_password' => 'dsfasdf',
            'smpt_port' => '222',
            'mail_gun_domain' => '',
            'mail_gun_secret' => '',
            'mandrill_secret' => '',
            'from_name' => '',
            'from_email' => ''
        ]);

    }

}
