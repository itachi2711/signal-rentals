<?php

namespace App\Providers;

use App\Events\InvoiceCreated;
use App\Events\LeaseCreated;
use App\Events\LeaseNextPeriod;
use App\Events\OverdueInvoiceChecked;
use App\Events\PaymentReceived;
use App\Listeners\CheckLeasePrePayment;
use App\Listeners\GenerateLeaseInvoice;
use App\Listeners\CalculatePenalty;
use App\Listeners\ProcessPayment;
use App\Listeners\SendNewInvoiceCommunication;
use App\Listeners\UpdatePropertyPeriod;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LeaseNextPeriod::class => [
            GenerateLeaseInvoice::class
        ],
        LeaseCreated::class => [
            GenerateLeaseInvoice::class
        ],
        PaymentReceived::class => [
            ProcessPayment::class
        ],
        InvoiceCreated::class => [
            UpdatePropertyPeriod::class,
            CheckLeasePrePayment::class,
            SendNewInvoiceCommunication::class
        ],
        OverdueInvoiceChecked::class => [
            CalculatePenalty::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
