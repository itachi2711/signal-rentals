<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 10:55 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;

class InvoicePaymentsController extends ApiController
{
    /**
     * @var PaymentInterface
     */
    protected $invoiceInterface, $load;

    /**
     * PaymentController constructor.
     * @param InvoiceInterface $invoiceInterface
     */
    public function __construct(InvoiceInterface $invoiceInterface)
    {
        $this->invoiceInterface = $invoiceInterface;
        $this->load = [];
    }

    /**
     * @param Invoice $invoice
     * @return mixed
     */
    public function index(Invoice $invoice)
    {
        $limit = $this->invoiceInterface->limit();
        $payments = $invoice->payment_transactions()->with([])->paginate($limit);
        if (isset($payments))
            return $this->respondWithData(PaymentResource::collection($payments));

        return $this->respondNotFound('Payment not found.');
    }

}

