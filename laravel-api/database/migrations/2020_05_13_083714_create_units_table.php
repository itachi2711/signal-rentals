<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();
            $table->string('agent_id', 36)->nullable();
            $table->string('property_id', 36);

            $table->string('unit_mode')->nullable(); // a unit can be commercial, or residential etc...e.g in a mixed used property
            $table->string('unit_type_id', 36);

            $table->string('unit_name');
            $table->double('rent_amount')->nullable();
            $table->integer('unit_floor')->nullable();
            $table->string('unit_status')->nullable(); // e.g vacant, occupied
            $table->string('description')->nullable();

            $table->string('bed_rooms')->nullable();
            $table->string('bath_rooms')->nullable();
            $table->string('total_rooms')->nullable();
            $table->string('square_foot')->nullable();

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
        Schema::dropIfExists('units');
    }
}
