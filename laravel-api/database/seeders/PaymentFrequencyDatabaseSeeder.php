<?php

namespace Database\Seeders;

use App\Models\PaymentFrequency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentFrequencyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('payment_frequencies')->delete();

        PaymentFrequency::create([
            'payment_frequency_name' => 'monthly',
            'payment_frequency_display_name' => 'Monthly',
            'payment_frequency_description' => "Monthly"
        ]);

        PaymentFrequency::create([
            'payment_frequency_name' => 'weekly',
            'payment_frequency_display_name' => 'Weekly',
            'payment_frequency_description' => "Weekly"
        ]);
    }
}
