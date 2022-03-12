<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 8/17/2021
 * Time: 12:11 AM
 */

namespace App\Http\Controllers\Api\Summary;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\PropertyResource;
use App\Models\GeneralSetting;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Unit;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\PeriodInterface;
use App\Rental\Repositories\Contracts\PropertyInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PropertyReportController extends ApiController
{
    /**
     * @var PropertyInterface
     */
    protected $propertyRepository, $invoiceRepository, $unitRepository, $periodRepository;

    /**
     * PropertyReportController constructor.
     * @param PropertyInterface $propertyInterface
     * @param InvoiceInterface $invoiceRepository
     * @param UnitInterface $unitRepository
     * @param PeriodInterface $periodRepository
     */
    public function __construct(PropertyInterface $propertyInterface, InvoiceInterface $invoiceRepository,
                                UnitInterface $unitRepository, PeriodInterface $periodRepository)
    {
        $this->propertyRepository = $propertyInterface;
        $this->unitRepository = $unitRepository;
        $this->periodRepository = $periodRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function report(Request $request)
    {
        $data = $request->all();
        $propertyID = $data['property_id'];
        $periodID = $data['period_id'];
        $property = $this->propertyRepository->getById($propertyID, ['units']);
        $period = $this->periodRepository->getById($periodID);

        $periodName = $period->name;
        $parsedDate = Carbon::parse($periodName)->subMonth();
        $previousPeriodName = date('F, Y', strtotime($parsedDate));
        $previousPeriod = $this->periodRepository->getLatestWhere('name', $previousPeriodName);

        $leases = Lease::where('property_id', $propertyID)->where('terminated_on', null)->with('units', 'tenants')->get();

        $propertyData = PropertyResource::make($property);
        $propertyData = $propertyData->resolve();
        $report = new \stdClass();
        $report->property = $propertyData;
        $report->period = $period;

        $totalPropertyBilled =  0.00;
        $totalPropertyPaid = 0.00;
        $totalPropertyPendingAmount =  0.00;
        foreach ($leases as $lease) {
            $currentPeriodInvoices = Invoice::where('lease_id', $lease->id)
                ->where('period_id', $periodID)->with('invoice_items')->get();
            $lease['current_invoices'] = $currentPeriodInvoices;

            if (isset($previousPeriod)) {
                $previousPeriodInvoices = Invoice::where('lease_id', $lease->id)
                    ->where('period_id', $previousPeriod->id)->with('invoice_items')->get();

                $invoiceAmount =  0.00;
                $amountPaid = 0.00;
                $pendingAmount =  0.00;
                foreach ($previousPeriodInvoices as $invoice) {
                    $invoiceAmount += $this->invoiceRepository->invoiceAmount($invoice->id);
                    $amountPaid += $this->invoiceRepository->paidAmount($invoice->id);
                    $pendingAmount += $this->invoiceRepository->pendingAmount($invoice->id);
                }
                $lease['previous_billing'] = [
                    'invoice_amount'    => format_money($invoiceAmount),
                    'amount_paid'       => format_money($amountPaid),
                    'pending_amount'    => format_money($pendingAmount),
                ];
            }

            $invoiceAmount =  0.00;
            $amountPaid = 0.00;
            $pendingAmount =  0.00;
            foreach ($currentPeriodInvoices as $invoice) {
                $invoiceAmount += $this->invoiceRepository->invoiceAmount($invoice->id);
                $amountPaid += $this->invoiceRepository->paidAmount($invoice->id);
                $pendingAmount += $this->invoiceRepository->pendingAmount($invoice->id);
            }

            $lease['current_billing'] = [
                'invoice_amount'    => format_money($invoiceAmount),
                'amount_paid'       => format_money($amountPaid),
                'pending_amount'    => format_money($pendingAmount),
            ];

            $totalPropertyBilled += $invoiceAmount;
            $totalPropertyPaid += $amountPaid;
            $totalPropertyPendingAmount += $pendingAmount;
        }
        $report->total_current_property_billing = [
            'total_billed'  => format_money($totalPropertyBilled),
            'total_paid'    => format_money($totalPropertyPaid),
            'total_pending' => format_money($totalPropertyPendingAmount),
        ];
        $report->leases = $leases;
      //  return $report;

        $settings = GeneralSetting::first();
        $file_path = $settings->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;

        $settings->logo_url = $local_path;
        $pdf = PDF::loadView('invoices.property-report', compact('report', 'settings'));
        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf->getDomPDF()->set_option("enable_php", true);

        // return view('invoices.invoice', compact('invoice'), compact('local_path'));
        return $pdf->download('report.pdf');
    }
}
