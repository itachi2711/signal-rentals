<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLateFeePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('late_fee_properties', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('late_fee_id');
            $table->uuid('property_id');

            $table->string('grace_period')->nullable();
            $table->double('late_fee_value')->nullable()->default(0);
            $table->string('late_fee_type')->nullable();
            $table->string('late_fee_frequency')->nullable();

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
        Schema::dropIfExists('late_fee_properties');
    }
}
