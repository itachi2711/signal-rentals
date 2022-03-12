<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 9/2/2021
 * Time: 8:16 PM
 */

namespace App\Http\Controllers\Api\Summary;

use App\Http\Controllers\Api\ApiController;
use App\Models\Payment;
use App\Rental\Repositories\Contracts\InvoiceInterface;

class TenantSummaryController extends ApiController
{
    protected $invoiceRepository;
    public function __construct(InvoiceInterface $invoiceRepository){
        $this->invoiceRepository = $invoiceRepository;
    }

    public function index(){
        $tenant = auth('tenants')->user();
        if (!isset($tenant))
            return [];
        $tenantID = $tenant->id;
        $leases = $tenant->leases;

        $pendingAmount = 0;
        foreach ($leases as $lease) {
            $pendingAmount = $pendingAmount + $this->invoiceRepository->pendingLeaseAmount($lease->id);
        }

        $payments = Payment::where('tenant_id', $tenantID)->limit(10)->latest()->get();
        $paymentData = [];
        foreach ($payments as $payment) {
            $paymentInfo = new \stdClass();
            $paymentInfo->date = format_date($payment->payment_date);
            $paymentInfo->amount = $payment->amount;
            $paymentData[] = $paymentInfo;
        }

        return [
            'pending_amount'    => format_money($pendingAmount),
            'total_leases'      => count($leases),
            'leases'            => $leases,
            'payment_data'      => $paymentData,
        ];
    }
}
