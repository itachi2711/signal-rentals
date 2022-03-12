<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:37 PM
 */

namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsTemplateSeeder extends Seeder
{

    public function run()
    {
        DB::table('sms_templates')->delete();

        // 1.
        SmsTemplate::create([
            'name' => NEW_TENANT,
            'display_name' => NEW_TENANT,
            'body' => "Hi {first_name}, Welcome.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}"
        ]);

        // 2.
        SmsTemplate::create([
            'name' => NEW_INVOICE,
            'display_name' => NEW_INVOICE,
            'body' => "Hi {first_name}, Invoice have been generated.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {period_name}, {invoice_date}, {due_date}, {invoice_number}"
        ]);

        // 3.
        SmsTemplate::create([
            'name' => DUE_INVOICE,
            'display_name' => DUE_INVOICE,
            'body' => "Hello {first_name}, Invoice is due",
            'tags' => "{first_name}"
        ]);

        // 4.
        SmsTemplate::create([
            'name' => OVER_DUE_INVOICE,
            'display_name' => OVER_DUE_INVOICE,
            'body' => "You have an overdue invoice",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {amount_applied}, {repayment_period}, {loan_type}, {interest_rate}"
        ]);

        // 5.
        SmsTemplate::create([
            'name' => NEW_LANDLORD,
            'display_name' => NEW_LANDLORD,
            'body' => "New landlord welcome",
            'tags' => "{first_name}, {middle_name}, {last_name}"
        ]);

        // 6.
        SmsTemplate::create([
            'name' => NEW_LEASE,
            'display_name' => NEW_LEASE,
            'body' => "New Lease created",
            'tags' => "{lease_number}, {start_date}, {rent_amount},{due_on}"
        ]);

        // 7.
        SmsTemplate::create([
            'name' => TERMINATE_LEASE,
            'display_name' => TERMINATE_LEASE,
            'body' => "TERMINATE_LEASE",
            'tags' => "{lease_number}, {start_date}, {rent_amount},{due_on}"
        ]);

        // 8.
        SmsTemplate::create([
            'name' => RECEIVE_PAYMENT,
            'display_name' => RECEIVE_PAYMENT,
            'body' => " RECEIVE_PAYMENT",
            'tags' => "{amount},{payment_date},{lease_number},{receipt_number}"
        ]);

        // 9.
        SmsTemplate::create([
            'name' => NEW_PROPERTY,
            'display_name' => NEW_PROPERTY,
            'body' => " NEW_PROPERTY",
            'tags' => "{property_code},{property_name}, {location}"
        ]);

        // 10.
        SmsTemplate::create([
            'name' => NEW_VACATE_NOTICE,
            'display_name' => NEW_VACATE_NOTICE,
            'body' => " NEW_VACATE_NOTICE",
            'tags' => "{vacating_date},{vacating_reason},{unit}"
        ]);

        // 11.
        SmsTemplate::create([
            'name' => RESET_PASSWORD,
            'display_name' => RESET_PASSWORD,
            'body' => "Your token to reset password is: {token}",
            'tags' => "{token}"
        ]);
    }

}
