<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/28/2021
 * Time: 3:15 PM
 */

namespace App\Listeners;

use App\Rental\Repositories\Contracts\LeaseInterface;

/**
 * Calculate Calculate their rent, utilities, extra charges to be paid for the next period.
 * Class CalculatePeriodicalLeasePayable
 * @package App\Listeners
 */
class GenerateLeaseInvoice
{
    /**
     * @var
     */
    protected $leaseRepository;

    /**
     * CalculatePeriodicalLeasePayable constructor.
     * @param LeaseInterface $leaseInterface
     */
    public function __construct(LeaseInterface $leaseInterface)
    {
        $this->leaseRepository = $leaseInterface;
    }

    /**
     *
     */
    public function handle()
    {
        $today = date('Y-m-d');
      //  $today = "2021-10-25";
        $this->leaseRepository->newLeaseInvoice($today);
    }
}
