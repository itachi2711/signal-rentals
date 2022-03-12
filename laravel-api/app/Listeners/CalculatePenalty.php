<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/22/2021
 * Time: 5:31 PM
 */

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalculatePenalty
{
    /**
     * @var
     */
    protected $leaseInterface;

    /**
     * ProcessPayment constructor.
     * @param LeaseInterface $leaseInterface
     */
    public function __construct(LeaseInterface $leaseInterface)
    {
        $this->leaseInterface = $leaseInterface;
    }

    /**
     * Handle the event.
     * @return void
     */
    public function handle()
    {
        $today = date('Y-m-d');
        $this->leaseInterface->calculateLateFees($today);
    }
}
