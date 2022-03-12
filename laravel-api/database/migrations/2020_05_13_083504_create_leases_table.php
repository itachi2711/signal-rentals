<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();
            $table->string('agent_id', 36)->nullable();
            $table->string('landlord_id', 36)->nullable();
            $table->string('property_id', 36);

            $table->string('lease_type_id');
            $table->string('lease_mode_id')->nullable();

            $table->string('lease_number')->nullable();

            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('due_date')->nullable();

            $table->string('next_billing_date')->nullable();
            $table->string('billed_on')->nullable();
            $table->string('terminated_on')->nullable();
            $table->string('terminated_by', 36)->nullable();

            $table->string('rent_amount')->nullable();
            $table->string('rent_deposit')->nullable();
            $table->string('billing_frequency')->nullable();
            $table->string('due_on')->nullable();
            $table->boolean('waive_penalty')->default(false);

            $table->boolean('skip_starting_period')->default(false);
            $table->integer('generate_invoice_on')->nullable(); // generate and send invoices
            $table->boolean('next_period_billing')->default(false); // when make invoice in june, is it billing for june or july

            $table->string('agreement_doc')->nullable();

            $table->string('invoice_number_prefix')->nullable();
            $table->string('invoice_footer')->nullable();
            $table->string('invoice_terms')->nullable();
            $table->boolean('show_payment_method_on_invoice')->default(false);

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
        Schema::dropIfExists('leases');
    }
}
