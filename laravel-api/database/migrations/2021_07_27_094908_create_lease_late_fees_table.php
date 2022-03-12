<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaseLateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_late_fees', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unique();
            $table->uuid('lease_id');
            $table->uuid('late_fee_id');
            $table->double('late_fee_value')->nullable()->default(0);
            $table->string('grace_period')->nullable();
            $table->string('late_fee_type')->nullable();
            $table->string('late_fee_frequency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lease_late_fees');
    }
}
