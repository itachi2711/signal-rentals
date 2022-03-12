<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 36)->primary()->unique();
            $table->string('agent_id', 36)->nullable();
            $table->string('tenant_type_id', 36)->nullable();

            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('gender')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('id_passport_number')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('tenant_number')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();

            $table->string('postal_code')->nullable();
            $table->string('postal_address')->nullable();
            $table->string('physical_address')->nullable();

            $table->string('next_of_kin_phone')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_relation')->nullable();

            $table->string('business_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('business_industry')->nullable();
            $table->string('business_description')->nullable();
            $table->string('business_address')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_email')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_postal_address')->nullable();
            $table->string('emergency_contact_physical_address')->nullable();

            $table->string('employment_status')->nullable();
            $table->string('employment_position')->nullable();
            $table->string('employer_contact_phone')->nullable();
            $table->string('employer_contact_email')->nullable();
            $table->string('employment_postal_address')->nullable();
            $table->string('employment_physical_address')->nullable();

            $table->string('rent_payment_contact')->nullable();
            $table->string('rent_payment_contact_postal_address')->nullable();
            $table->string('rent_payment_contact_physical_address')->nullable();

            $table->string('profile_pic')->nullable();

            $table->boolean('password_set')->default(false);
            $table->string('password', 60)->nullable();
            $table->boolean('confirmed')->default(false);
            $table->string('confirmation_code')->nullable();

            $table->string('created_by', 36)->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('tenants');
    }
}
