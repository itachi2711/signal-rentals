<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 10:55 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\InvoiceResource;
use App\Models\Lease;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;

class LeaseInvoicesController extends ApiController
{
    /**
     * @var PaymentInterface
     */
    protected $leaseRepository, $load, $transactionRepository;

    /**
     * LeaseInvoicesController constructor.
     * @param LeaseInterface $leaseRepository
     * @param TransactionInterface $transactionRepository
     */
    public function __construct(LeaseInterface $leaseRepository, TransactionInterface $transactionRepository)
    {
        $this->leaseRepository = $leaseRepository;
        $this->transactionRepository = $transactionRepository;
        $this->load = [];
    }

    /**
     * @param Lease $lease
     * @return mixed
     */
    public function index(Lease $lease)
    {
        $limit = $this->leaseRepository->limit();
        $invoices = $lease->invoices()->with([])->paginate($limit);
        if (isset($invoices)) {
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

