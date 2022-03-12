<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/21/2021
 * Time: 8:56 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Resources\InvoiceResource;
use App\Models\Landlord;
use App\Models\Invoice;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;

class LandlordInvoicesController extends ApiController
{
    /**
     * @var PaymentInterface
     */
    protected $landlordRepository, $load, $loginProxy, $transactionRepository;

    /**
     * LandlordInvoicesController constructor.
     * @param LandlordInterface $landlordRepository
     * @param LoginProxy $loginProxy
     * @param TransactionInterface $transactionRepository
     */
    public function __construct(LandlordInterface $landlordRepository,
                                LoginProxy $loginProxy,
                                TransactionInterface $transactionRepository)
    {
        $this->landlordRepository = $landlordRepository;
        $this->transactionRepository = $transactionRepository;
        $this->loginProxy = $loginProxy;
        $this->load = [];
    }

    /**
     * @param Landlord $landlord
     * @return mixed
     */
    public function index(Landlord $landlord)
    {
        if ($this->loginProxy->checkLandlord($landlord)) {
            $limit = $this->landlordRepository->limit();
            $invoices = $landlord->invoices()->with([])->paginate($limit);
            if (isset($invoices)) {
                $data = InvoiceResource::collection($invoices);
                $data->map(function($item) {
                    $item['amount_paid'] =  $this->transactionRepository->invoicePaidAmount($item['id']);
                    return $item;
                });
                return $this->respondWithData(InvoiceResource::collection($invoices));
            }
            return $this->respondNotFound('Invoices not found.');
        }
        return $this->respondNotFound('Invoices not found.');
    }

    /**
     * @param Landlord $landlord
     * @param Invoice $invoice
     * @return mixed
     */
    public function show(Landlord $landlord, Invoice $invoice)
    {
        if ($this->loginProxy->checkLandlord($landlord)) {
            $data = $landlord->invoices()->where('invoices.id', $invoice->id)->first();

            if (isset($data))
                return $this->respondWithData(InvoiceResource::make($data));

            return $this->respondNotFound('Invoice not found.');
        }
        return $this->respondNotFound('Invoice not found.');
    }

}

