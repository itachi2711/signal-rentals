<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/10/2021
 * Time: 9:48 AM
 */

namespace Database\Seeders;

use App\Models\SystemNotification;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SystemNotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('system_notifications')->delete();

        // 1.
        SystemNotification::create([
            'name'          => NEW_TENANT,
            'display_name'  => NEW_TENANT
        ]);

        // 2.
        SystemNotification::create([
            'name'          => NEW_INVOICE,
            'display_name'  => NEW_INVOICE
        ]);

        // 3.
        SystemNotification::create([
            'name'          => DUE_INVOICE,
            'display_name'  => DUE_INVOICE
        ]);

        // 4.
        SystemNotification::create([
            'name'          => OVER_DUE_INVOICE,
            'display_name'  => OVER_DUE_INVOICE
        ]);

        // 5.
        SystemNotification::create([
            'name'          => NEW_LANDLORD,
            'display_name'  => NEW_LANDLORD
        ]);

        // 6.
        SystemNotification::create([
            'name'          => NEW_LEASE,
            'display_name'  => NEW_LEASE
        ]);

        // 7.
        SystemNotification::create([
            'name'          => TERMINATE_LEASE,
            'display_name'  => TERMINATE_LEASE
        ]);

        // 8.
        SystemNotification::create([
            'name'          => RECEIVE_PAYMENT,
            'display_name'  => RECEIVE_PAYMENT
        ]);

        // 9.
        SystemNotification::create([
            'name'          => NEW_PROPERTY,
            'display_name'  => NEW_PROPERTY
        ]);

        // 10.
        SystemNotification::create([
            'name'          => NEW_VACATE_NOTICE,
            'display_name'  => NEW_VACATE_NOTICE
        ]);

        // 11.
        SystemNotification::create([
            'name'          => RESET_PASSWORD,
            'display_name'  => RESET_PASSWORD
        ]);
    }
}
