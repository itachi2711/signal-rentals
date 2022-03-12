<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary();
            $table->string('agent_id', 36)->nullable();
            $table->string('payment_method_id', 36);
            $table->string('currency_id', 36)->nullable();
            $table->string('tenant_id', 36);
            $table->string('lease_id', 36);
            $table->string('property_id', 36)->nullable();
            $table->string('lease_number');

            $table->dateTime('payment_date');
            $table->integer('amount');
            $table->string('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->string('receipt_number');
            $table->string('paid_by')->nullable();
            $table->string('reference_number')->nullable();

          //  $table->string('payment_status')->nullable();
            $table->enum('payment_status',
                [
                    'approved',
                    'pending',
                    'cancelled'
                ])->default('pending');
            $table->string('cancel_notes')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->string('approved_by')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

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
        Schema::dropIfExists('payments');
    }
}
