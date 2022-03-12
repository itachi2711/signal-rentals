<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentMethodDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('payment_methods')->delete();

        PaymentMethod::create([
            'payment_method_name' => 'cash',
            'payment_method_display_name' => 'Cash',
            'payment_method_description' => "Cash"
        ]);
    }
}
