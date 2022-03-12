<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 10:23 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\InvoiceResource;
use App\Models\Property;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\PropertyInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;

class PropertyInvoicesController extends ApiController
{
    /**
     * @var InvoiceInterface
     */
    protected $propertyRepository, $load, $transactionRepository;

    /**
     * PropertyInvoicesController constructor.
     * @param PropertyInterface $propertyInterface
     * @param TransactionInterface $transactionRepository
     */
    public function __construct(PropertyInterface $propertyInterface,
                                TransactionInterface $transactionRepository)
    {
        $this->propertyRepository = $propertyInterface;
        $this->transactionRepository = $transactionRepository;
        $this->load = [];
    }

    /**
     * @param Property $property
     * @return mixed
     */
    public function index(Property $property)
    {
        $limit = $this->propertyRepository->limit();
        $invoices = $property->invoices()->with([])->paginate($limit);
        if (isset($invoices))
        {
            $data = InvoiceResource::collection($invoices);
            $data->map(function($item) {
                $item['amount_paid'] =  $this->transactionRepository->invoicePaidAmount($item['id']);
                return $item;
            });
            return $this->respondWithData(InvoiceResource::collection($invoices));
        }
        return $this->respondNotFound('Invoice not found.');
    }
}
