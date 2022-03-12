<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
          //  $table->increments('id')->unique();

            $table->string('id', 36)->primary()->unique();
            $table->string('agent_id', 36)->nullable();
            $table->string('invoice_id', 36)->nullable()->default('');

            $table->string('lease_id', 36)->nullable();
            $table->string('property_id', 36)->nullable();

            $table->string('item_name')->nullable();
            $table->string('item_type')->nullable();
            $table->string('item_description')->nullable();

            $table->string('quantity')->nullable();
            $table->double('price')->default(0);
            $table->double('amount')->default(0);
            $table->double('discount')->default(0);
            $table->double('tax')->default(0);
            $table->string('tax_id', 36)->nullable()->default('');

            $table->string('paid_on')->nullable()->default(NULL);

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
        Schema::dropIfExists('invoice_items');
    }
}
