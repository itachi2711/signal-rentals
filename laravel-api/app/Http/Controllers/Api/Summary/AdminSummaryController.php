<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 9/2/2021
 * Time: 9:24 PM
 */

namespace App\Http\Controllers\Api\Summary;


use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\InvoiceResource;
use App\Models\Lease;
use App\Models\Period;
use App\Models\Property;
use App\Models\Tenant;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;

class AdminSummaryController extends ApiController
{
    protected $invoiceRepository, $transactionRepository;

    /**
     * AdminSummaryController constructor.
     * @param InvoiceInterface $invoiceRepository
     * @param TransactionInterface $transactionRepository
     */
    public function __construct(InvoiceInterface $invoiceRepository,
                                TransactionInterface $transactionRepository){
        $this->invoiceRepository = $invoiceRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function index(){
        $admin = auth('api')->user();
        if (!isset($admin))
            return [];

        $properties = Property::with('units')->get();
        $totalUnits = 0;
        foreach ($properties as $property) {
            $totalUnits = $totalUnits + count($property->units);
        }

        $totalLeases = Lease::where('terminated_on', null)->count();
        $totalTenants = Tenant::count();

        $leases = Lease::where('terminated_on', null)->get();
        $pendingAmount = 0;
        foreach ($leases as $lease) {
            $pendingAmount = $pendingAmount + $this->invoiceRepository->pendingLeaseAmount($lease->id);
        }

        // last six period
        $periods = Period::latest()->limit(6)->get();
        $periodicalBillingSummary = [];
        foreach ($periods as $period) {
            if (isset($period)) {
                $invoices = InvoiceResource::collection(
                    $this->invoiceRepository->getWhere('period_id', $period['id'], ['lease'], true)
                );
                $amountPaid = 0.00;
                $amountDue =  0.00;
                $amountBilled =  0.00;
                foreach ($invoices as $invoice) {
                    $invoiceData = $invoice->resolve();
                    $amountBilled += $invoiceData['summary']['invoice_amount_number'];
                    $amountPaid += $this->transactionRepository->invoicePaidAmount($invoice['id']);
                    $amountDue = $amountBilled - $amountPaid;
                }

                $billingInfo = new \stdClass();
                $billingInfo->period_id = $period['id'];
                $billingInfo->period_name = $period['name'];
                $billingInfo->amount_billed = $amountBilled;
                $billingInfo->amount_paid = $amountPaid;
                $billingInfo->amount_due = $amountDue;

				$billingInfo->amount_billed_as_currency = format_money($amountBilled);
				$billingInfo->amount_paid_as_currency = format_money($amountPaid);
				$billingInfo->amount_due_as_currency = format_money($amountDue);

                $periodicalBillingSummary[] = $billingInfo;
            }
        }
        return [
            'total_properties'          => count($properties),
            'total_units'               => $totalUnits,
            'total_leases'              => $totalLeases,
            'total_tenants'             => $totalTenants,
            'pending_amount'            => format_money($pendingAmount),
            'periodical_billing'        => $periodicalBillingSummary,
        ];
    }
}
