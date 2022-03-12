<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Landlord;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LandlordDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('landlords')->delete();

        Landlord::create([
            'email' => 'landlord@landlord.com',
            'first_name' => 'Landlord',
            'last_name' => 'Landlord',
            'password' => 'landlord',
        ]);
    }
}
