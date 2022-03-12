<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 36)->primary()->unique();

            $table->string('agent_id', 36)->nullable();
            $table->string('property_id', 36);
            $table->string('lease_id', 36);
            $table->string('period_id', 36);
            $table->string('period_name', 36);

            $table->dateTime('invoice_date');
            $table->dateTime('due_date');
            $table->dateTime('paid_on')->nullable();

            $table->string('status')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('currency')->nullable();

            $table->string('terms')->nullable();
            $table->string('notes')->nullable();

            $table->double('total_items')->default(0);
            $table->double('sub_total')->default(0);
            $table->double('total_tax')->default(0);
            $table->double('total_discount')->default(0);
            $table->double('invoice_amount')->default(0);

            $table->dateTime('late_fee_charged_on')->nullable();

            $table->string('created_by', 36)->nullable()->default('');
            $table->string('updated_by', 36)->nullable()->default('');

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
        Schema::dropIfExists('invoices');
    }
}
