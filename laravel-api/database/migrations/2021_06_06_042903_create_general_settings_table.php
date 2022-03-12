<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();
            $table->string('company_name');
            $table->string('company_type')->nullable();
            $table->string('email')->nullable();
            $table->string('currency')->nullable();
            $table->string('phone')->nullable();
            $table->string('physical_address')->nullable();
            $table->string('postal_address')->nullable();
            $table->string('website_url')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            $table->string('date_format')->nullable();
            $table->string('amount_thousand_separator')->nullable();
            $table->string('amount_decimal_separator')->nullable();
            $table->string('amount_decimal')->nullable();

            $table->string('theme')->nullable();
            $table->string('language')->nullable();

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
        Schema::dropIfExists('general_settings');
    }
}
