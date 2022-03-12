<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 5/28/2021
 * Time: 8:26 AM
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RentalServiceProvider extends ServiceProvider
{

    /**
     * System repositories
     * @var array
     */
    protected $repositories = [
        'Agent',
        'AccountClass',
        'AccountLedger',
        'AccountType',
        'User',
        'User',
        'Invoice',
        'InvoiceItem',
        'Currency',
        'Payment',
        'Role',
        'Permission',
        'GeneralSetting',
        'Account',
        'Amenity',
        'ExtraCharge',
        'Fee',
        'Journal',
        'Landlord',
        'Lease',
        'LeaseMode',
		'LeaseType',
        'Ledger',
        'PaymentFrequency',
        'PaymentMethod',
        'PropertyCategory',
        'Property',
        'PropertyType',
        'Task',
        'TaskCategory',
        'Tenant',
        'TenantType',
        'Unit',
        'UnitType',
        'UnitUtility',
        'UnitUtilityBill',
        'UtilityBill',
        'Utility',
        'VacationNotice',
        'LeaseSetting',
        'TenantSetting',
        'PropertySetting',
        'CommunicationSetting',
        'SmsConfigSetting',
        'EmailConfigSetting',
        'EmailTemplate',
        'SmsTemplate',
        'Reading',
        'Transaction',
        'SystemNotification',
        'Period',
        'LateFee',
        'Waiver'
    ];

    /**
     *  Loops through all repositories and binds them with their Eloquent implementation
     */
    public function register()
    {
        array_walk($this->repositories, function ($repository) {
            $this->app->bind(
                'App\Rental\Repositories\Contracts\\' . $repository . 'Interface',
                'App\Rental\Repositories\Eloquent\\' . $repository . 'Repository'
            );
        });
    }
}
