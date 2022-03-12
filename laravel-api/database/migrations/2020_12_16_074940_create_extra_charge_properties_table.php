<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtraChargePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_charge_properties', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('extra_charge_id');
            $table->uuid('property_id');

            $table->string('extra_charge_value')->nullable();
            $table->string('extra_charge_type')->nullable();
            $table->string('extra_charge_frequency')->nullable();

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
        Schema::dropIfExists('extra_charge_properties');
    }
}
