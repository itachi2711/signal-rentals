<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/16/2021
 * Time: 5:23 PM
 */

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Rental\Repositories\Contracts\PaymentInterface;
use App\Rental\Repositories\Contracts\PropertyInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdatePropertyPeriod
{
    /**
     * @var
     */
    protected $paymentRepository, $propertyRepository;

    /**
     * UpdatePropertyPeriod constructor.
     * @param PaymentInterface $paymentInterface
     * @param PropertyInterface $propertyRepository
     */
    public function __construct(PaymentInterface $paymentInterface, PropertyInterface $propertyRepository)
    {
        $this->paymentRepository = $paymentInterface;
        $this->propertyRepository = $propertyRepository;
    }

    /**
     * @param InvoiceCreated $event
     */
    public function handle(InvoiceCreated $event)
    {
        $invoice = $event->invoice;
        $periodID = $invoice['period_id'];
        $propertyID = $invoice['property_id'];

        $property = $this->propertyRepository->getById($propertyID);

        if ($property)
            $property->periods()->syncWithoutDetaching([$periodID]);
    }
}
