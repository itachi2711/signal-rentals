<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->unique()->primary();

            $table->string('agent_id', 36)->nullable();
            $table->string('invoice_id', 36);
            $table->string('invoice_item_id', 36);
            $table->string('payment_id', 36)->nullable();
            $table->string('waiver_id', 36)->nullable();
            $table->string('transaction_date');
            $table->string('transaction_amount');

            $table->string('invoice_item_type')->nullable();
            $table->string('transaction_type')->nullable();

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
        Schema::dropIfExists('transactions');
    }
}
