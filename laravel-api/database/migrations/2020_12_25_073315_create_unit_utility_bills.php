<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitUtilityBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_utility_bills', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id')->unique();
            $table->string('agent_id', 36)->nullable();

            $table->string('unit_id', 36)->nullable();
            $table->string('utility_bill_id', 36)->nullable();

            $table->string('reading_date')->nullable();
            $table->string('current_reading')->nullable();

            $table->string('property_id', 36)->nullable();
            $table->string('utility_id', 36)->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

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
        Schema::dropIfExists('unit_utility_bills');
    }
}
