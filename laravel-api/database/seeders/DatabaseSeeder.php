<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected $seeders = [
        PermissionSeeder::class,
        RoleSeeder::class,
        UsersTableSeeder::class,
        CurrencySeeder::class,
        AccountClassSeeder::class,
       // LandlordDatabaseSeeder::class,
        ExtraChargeDatabaseSeeder::class,
        LateFeeDatabaseSeeder::class,
        LeaseModeDatabaseSeeder::class,
        LeaseTypeDatabaseSeeder::class,
        PaymentFrequencyDatabaseSeeder::class,
        PaymentMethodDatabaseSeeder::class,
        PropertyTypeDatabaseSeeder::class,
       // TenantDatabaseSeeder::class,
        TenantTypeDatabaseSeeder::class,
        UnitTypeDatabaseSeeder::class,
        UtilityDatabaseSeeder::class,
        AmenityDatabaseSeeder::class,
        EmailSettingTableSeeder::class,
        EmailTemplateSeeder::class,
        CommunicationSettingSeeder::class,
        SmsTemplateSeeder::class,
        LeaseSettingSeeder::class,
        GeneralSettingSeeder::class,
        SystemNotificationTableSeeder::class,
        TenantSettingSeeder::class
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->seeders as $seedClass) {
            $this->call($seedClass);
        }
    }
}
