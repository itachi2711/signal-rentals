<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 9/2/2021
 * Time: 8:54 PM
 */

namespace App\Http\Controllers\Api\Summary;


use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Period;
use App\Models\Property;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;

class LandlordSummaryController extends ApiController
{
    protected $invoiceRepository, $transactionRepository;

    /**
     * LandlordSummaryController constructor.
     * @param InvoiceInterface $invoiceRepository
     * @param TransactionInterface $transactionRepository
     */
    public function __construct(InvoiceInterface $invoiceRepository,
                                TransactionInterface $transactionRepository){
        $this->invoiceRepository = $invoiceRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function index(){
        $landlord = auth('landlords')->user();
        if (!isset($landlord))
            return [];
        $landlordID = $landlord->id;

        $properties = Property::where('landlord_id', $landlordID)->with('units')->get();
        $totalUnits = 0;
        foreach ($properties as $property) {
            $totalUnits = $totalUnits + count($property->units);
        }

        $leases = $landlord->leases;
        $pendingAmount = 0;
        foreach ($leases as $lease) {
            $pendingAmount = $pendingAmount + $this->invoiceRepository->pendingLeaseAmount($lease->id);
        }

        // last six period
        $invoicesRaw = $landlord->invoices;
        $periods = Period::latest()->limit(6)->get();
        $periodicalBillingSummary = [];
        foreach ($periods as $period) {
            if (isset($period)) {
                $invoices = InvoiceResource::collection(
                    $invoicesRaw->where('period_id', $period['id'])
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
            'total_properties'              => count($properties),
            'total_units'                   => $totalUnits,
            'total_leases'                  => count($leases),
            'pending_amount'                => $pendingAmount,
            'pending_amount_as_currency'    => format_money($pendingAmount),
            'periodical_billing'            => $periodicalBillingSummary,
        ];
    }
}
