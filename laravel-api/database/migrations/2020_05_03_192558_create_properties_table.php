<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 36)->primary()->unique();
            $table->string('agent_id', 36)->nullable();
            $table->string('landlord_id', 36)->nullable();
            $table->string('property_code'); // user generated 2 to 4 digit code - easier as property names may generate duplicates

            $table->string('property_type_id', 36); // apartment, commercial, house, duplex, mixed use

            $table->string('property_name')->nullable();

            $table->string('location');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();

            $table->string('agent_commission_value')->nullable();
            $table->string('agent_commission_type')->nullable();

            $table->string('created_by', 36)->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('properties');
    }
}
