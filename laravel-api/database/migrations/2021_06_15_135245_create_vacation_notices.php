<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacationNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_notices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary();
            $table->string('agent_id', 36)->nullable();
            $table->string('tenant_id', 36);
            $table->string('lease_id', 36);
            $table->string('property_id', 36)->nullable();
            $table->string('unit')->nullable();

            $table->string('date_received');
            $table->string('vacating_date');
            $table->string('vacating_reason')->nullable();

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
        Schema::dropIfExists('vacation_notices');
    }
}
