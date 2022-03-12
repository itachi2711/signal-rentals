<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaseSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary();
            $table->string('agent_id', 36)->nullable();

            $table->string('lease_number_prefix')->nullable();
            $table->string('invoice_number_prefix')->nullable();
            $table->string('invoice_footer')->nullable();
            $table->string('invoice_terms')->nullable();
            $table->integer('generate_invoice_on')->nullable();
            $table->boolean('show_payment_method_on_invoice')->default(false);
            $table->boolean('next_period_billing')->default(false);
            $table->boolean('skip_starting_period')->default(false);
            $table->boolean('waive_penalty')->default(false);

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
        Schema::dropIfExists('lease_settings');
    }
}
