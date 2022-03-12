<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/16/2021
 * Time: 9:19 PM
 */

namespace App\Http\Controllers\Api\Summary;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Resources\InvoiceResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\InvoiceItemInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\PeriodInterface;
use App\Rental\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;

class PeriodBillingController extends ApiController
{
    /**
     * @var string[]
     */
    protected $load, $periodRepository, $invoiceRepository,
        $invoiceItemRepository, $loginProxy, $landlordRepository, $userRepository, $leaseRepository;

    /**
     * PeriodBillingController constructor.
     * @param PeriodInterface $periodRepository
     * @param InvoiceInterface $invoiceRepository
     * @param LandlordInterface $landlordRepository
     * @param UserInterface $userRepository
     * @param InvoiceItemInterface $invoiceItemRepository
     * @param LeaseInterface $leaseRepository
     * @param LoginProxy $loginProxy
     */
    public function __construct(PeriodInterface $periodRepository,
                                InvoiceInterface $invoiceRepository,
                                LandlordInterface $landlordRepository,
                                UserInterface $userRepository,
                                InvoiceItemInterface $invoiceItemRepository,
                                LeaseInterface $leaseRepository,
                                LoginProxy $loginProxy)
    {
        $this->periodRepository = $periodRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceItemRepository = $invoiceItemRepository;
        $this->landlordRepository = $landlordRepository;
        $this->userRepository = $userRepository;
        $this->loginProxy = $loginProxy;
        $this->leaseRepository = $leaseRepository;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $period = $this->periodRepository->getLatestFirst();
        if(!$period)
            return null;

        $invoices = InvoiceResource::collection(
            $this->invoiceRepository->getWhere('period_id', $period['id'], [], true)
        );

        $amountPaid = 0.00;
        $amountDue =  0.00;
        $amountBilled =  0.00;
        foreach ($invoices as $invoice) {
         //   $invoiceData = $invoice->toArray($request);
            $invoiceData = $invoice->resolve();
            $amountBilled += $invoiceData['summary']['invoice_amount_number'];
            $amountPaid += $invoiceData['summary']['amount_paid_number'];
            $amountDue += $invoiceData['summary']['amount_due_number'];
        }

        return [
            'period_id'     => $period['id'],
            'amount_billed' => format_money($amountBilled),
            'amount_paid'   => format_money($amountPaid),
            'amount_due'    => format_money($amountDue)
        ];
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function propertyBilling(Request $request)
    {
        $data = $request->all();
        $userID     = $data['user_id'];
        $propertyID = $data['property_id'];
        $periodID   = $data['period_id'];

        $admin = $this->userRepository->getByID($userID);
        if (isset($admin)) {
            // invoices for active leases
            $activeLeases = $this->leaseRepository->fetchActive(['invoices'], '', $propertyID);
            return $this->calculateLeasesSummary($activeLeases, $propertyID, $periodID);
        }

        $landlord = $this->landlordRepository->getById($userID);
        if (isset($landlord)) {
            if ($this->loginProxy->checkLandlord($landlord)) {
                // invoices for landlord's active leases
                $activeLeases = $this->leaseRepository->fetchActive(['invoices'], $landlord['id'], $propertyID);
                return $this->calculateLeasesSummary($activeLeases, $propertyID, $periodID);
            }
        }
        return null;
    }

    /**
     * @param $activeLeases
     * @param $propertyID
     * @param $periodID
     * @return array
     */
    private function calculateLeasesSummary($activeLeases, $propertyID, $periodID)
    {
        $invoicesData = [];
        foreach ($activeLeases as $lease) {
            $invoices = $lease['invoices'];
            foreach ($invoices as $invoice) {
                $invoicesData[] = $invoice;
            }
        }
        $periodInvoices =  collect($invoicesData)->where('period_id', $periodID)->all();
        $invoices = InvoiceResource::collection($periodInvoices);

        $amountPaid =  0.00;
        $amountDue =  0.00;
        $amountBilled =  0.00;
        foreach ($invoices as $invoice) {
            $invoiceData = $invoice->resolve();
            $amountBilled += $invoiceData['summary']['invoice_amount_number'];
            $amountPaid += $invoiceData['summary']['amount_paid_number'];
            $amountDue += $invoiceData['summary']['amount_due_number'];
        }
        return [
            'period_id'     => $periodID,
            'property_id'   => $propertyID,
            'amount_billed' => format_money($amountBilled),
            'amount_paid'   => format_money($amountPaid),
            'amount_due'    => format_money($amountDue)
        ];
    }
}
