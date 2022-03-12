<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:35 PM
 */

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{

    public function run()
    {
        DB::table('email_templates')->delete();

        // 1.
        EmailTemplate::create([
            'name' => NEW_TENANT,
            'display_name' => NEW_TENANT,
            'subject' => 'New tenant Welcome',
            'body' => "Hi {first_name}, Welcome.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}"
        ]);

        // 2.
        EmailTemplate::create([
            'name' => NEW_INVOICE,
            'display_name' => NEW_INVOICE,
            'subject' => 'Invoice have been generated',
            'body' => "Hi {first_name}, Invoice have been generated.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {period_name}, {invoice_date}, {due_date}, {invoice_number}"
        ]);

        // 3.
        EmailTemplate::create([
            'name' => DUE_INVOICE,
            'display_name' => DUE_INVOICE,
            'subject' => 'Invoice is due',
            'body' => "Hello {first_name}, Invoice is due",
            'tags' => "{first_name}"
        ]);

        // 4.
        EmailTemplate::create([
            'name' => OVER_DUE_INVOICE,
            'display_name' => OVER_DUE_INVOICE,
            'subject' => 'Over Due invoice',
            'body' => "You have an overdue invoice",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {amount_applied}, {repayment_period}, {loan_type}, {interest_rate}"
        ]);

        // 5.
        EmailTemplate::create([
            'name' => NEW_LANDLORD,
            'display_name' => NEW_LANDLORD,
            'subject' => 'New landlord welcome',
            'body' => "New landlord welcome",
            'tags' => "{first_name}, {middle_name}, {last_name}"
        ]);

        // 6.
        EmailTemplate::create([
            'name' => NEW_LEASE,
            'display_name' => NEW_LEASE,
            'subject' => 'New Lease created',
            'body' => "New Lease created",
            'tags' => "{lease_number}, {start_date}, {rent_amount},{due_on}"
        ]);

        // 7.
        EmailTemplate::create([
            'name' => TERMINATE_LEASE,
            'display_name' => TERMINATE_LEASE,
            'subject' => 'TERMINATE_LEASE.',
            'body' => "TERMINATE_LEASE",
            'tags' => "{lease_number}, {start_date}, {rent_amount}"
        ]);

        // 8.
        EmailTemplate::create([
            'name' => RECEIVE_PAYMENT,
            'display_name' => RECEIVE_PAYMENT,
            'subject' => 'RECEIVE_PAYMENT',
            'body' => " RECEIVE_PAYMENT",
            'tags' => "{amount},{payment_date},{lease_number},{receipt_number}"
        ]);

        // 9.
        EmailTemplate::create([
            'name' => NEW_PROPERTY,
            'display_name' => NEW_PROPERTY,
            'subject' => 'NEW_PROPERTY',
            'body' => " NEW_PROPERTY",
            'tags' => "{property_code},{property_name}, {location}"
        ]);

        // 10.
        EmailTemplate::create([
            'name' => NEW_VACATE_NOTICE,
            'display_name' => NEW_VACATE_NOTICE,
            'subject' => 'NEW_VACATE_NOTICE',
            'body' => " NEW_VACATE_NOTICE",
            'tags' => "{vacating_date},{vacating_reason},{unit}"
        ]);

        // 11.
        EmailTemplate::create([
            'name' => RESET_PASSWORD,
            'display_name' => RESET_PASSWORD,
            'subject' => 'RESET_PASSWORD',
            'body' => "Your token to reset password is: {token}",
            'tags' => "{token}"
        ]);
    }
}
