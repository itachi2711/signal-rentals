<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaypalPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paypal_payments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            // $table->bigIncrements('id');
            $table->uuid('id')->unique()->primary();

            $table->string('merchant_id')->nullable();

            $table->string('payment_id')->nullable();
            $table->string('payer_id')->nullable();
            $table->string('payer_email')->nullable();
            $table->float('amount', 10, 2)->nullable();
            //  $table->string('currency')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('status')->nullable();

            $table->string('user_seller_id')->nullable();
            $table->string('pay_order_no')->nullable();
            $table->string('order_name')->nullable();
            $table->string('total_fee')->nullable();
            $table->string('body')->nullable();
            $table->string('notify_url')->nullable();
            $table->string('return_url')->nullable();
            $table->string('pay_type')->nullable();
            $table->string('sign')->nullable();
            $table->string('currency')->nullable();
            $table->string('http_referer')->nullable();

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
        Schema::dropIfExists('paypal_payments');
    }
}
