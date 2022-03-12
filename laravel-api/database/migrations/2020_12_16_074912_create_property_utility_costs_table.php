<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyUtilityCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_utility_costs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('utility_id'); // maybe just name it here?
            $table->uuid('property_id');

            $table->string('utility_unit_cost')->nullable();
            $table->string('utility_base_fee')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_utility_costs');
    }
}
