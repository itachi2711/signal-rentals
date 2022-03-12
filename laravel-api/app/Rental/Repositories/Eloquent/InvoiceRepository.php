<?php

namespace App\Rental\Repositories\Eloquent;

use App\Events\InvoiceCreated;
use App\Http\Resources\InvoiceResource;
use App\Models\GeneralSetting;
use App\Models\LeaseSetting;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Models\Invoice;
use App\Rental\Repositories\Contracts\InvoiceItemInterface;
use App\Rental\Repositories\Contracts\JournalInterface;
use App\Rental\Repositories\Contracts\PeriodInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\This;

class InvoiceRepository extends BaseRepository implements InvoiceInterface
{
    protected $model, $periodRepository, $transactionRepository;

    /**
     * InvoiceRepository constructor.
     * @param Invoice $model
     * @param PeriodInterface $periodRepository
     * @param TransactionInterface $transactionRepository
     */
    function __construct(Invoice $model,
                         PeriodInterface $periodRepository,
                         TransactionInterface $transactionRepository)
    {
        $this->model = $model;
        $this->periodRepository = $periodRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @return Invoice
     */
	public function getModel()
	{
		return $this->model;
	}

    /**
     * @param $leaseID
     * @return mixed
     */
	public function oldestUnpaid($leaseID)
    {
       return $this->model->where('paid_on', null)->where('lease_id', $leaseID)->oldest()->first();
    }

    /**
     * @param $invoiceDate
     * @param $lease
     * @param bool $penaltyInvoice
     * @return mixed
     */
    public function newInvoice ($invoiceDate, $lease, $penaltyInvoice = false) {
        $period = $this->periodRepository->getPeriod($invoiceDate, $lease);
        $dueDate = $this->calculateDueDate($invoiceDate, $lease);

        if ($penaltyInvoice)
            $dueDate = $invoiceDate;
        return $this->model->create([
            'lease_id'      => $lease['id'],
            'property_id'   => $lease['property_id'],
            'period_id'     => $period['id'],
            'period_name'   => $period['name'],
            'invoice_date'  => $invoiceDate,
            'due_date'      => $dueDate
        ]);
    }

    /**
     * @param $today
     * @param $lease
     * @return string
     */
    private function calculateDueDate($today, $lease)
    {
        $dueDay = $lease['due_on']; // day of month

        /// For the first time we are billing, invoice due date is the date the lease starts
        if (is_null($lease['billed_on'])) {
            return $lease['start_date'];
        }
        return  Carbon::parse($today)
            ->addMonthsNoOverflow()
            ->setUnitNoOverflow('day', $dueDay, 'month')
            ->format('Y-m-d');
    }

    /**
     * @param $leaseID
     * @return mixed
     */
    public function pendingInvoicesByLeaseId($leaseID)
    {
        return $this->model
            ->where('lease_id', $leaseID)
            ->where('paid_on', null)
            ->get();
    }

    /**
     * @param $leaseID
     * @return bool
     */
    public function leaseHasPendingInvoices($leaseID)
    {
        $invoices = $this->model
            ->where('lease_id', $leaseID)
            ->where('paid_on', null)
            ->get();
        if (isset($invoices) && count($invoices) > 0)
            return true;
        return false;
    }

    /**
     * @param $invoiceID
     * @return mixed
     */
    public function invoiceAmount($invoiceID)
    {
        return DB::table('invoice_items')
            ->select(DB::raw('COALESCE(sum(invoice_items.amount), 0.0) as totalAmount'))
            ->where('invoice_id', $invoiceID)
            ->first()->totalAmount;
    }

    /**
     * @param $invoiceID
     * @return mixed
     */
    public function paidAmount($invoiceID)
    {
        return $this->transactionRepository->invoicePaidAmount($invoiceID);
    }

    /**
     * @param $invoiceID
     * @return mixed
     */
    public function pendingAmount($invoiceID)
    {
        $paidAmount = $this->transactionRepository->invoicePaidAmount($invoiceID);
        $invoiceAmount = $this->invoiceAmount($invoiceID);
        return $invoiceAmount - $paidAmount;
    }

    /**
     * @param $leaseID
     * @return int|mixed
     */
    public function pendingLeaseAmount($leaseID)
    {
        $pendingInvoices = $this->model
            ->where('lease_id', $leaseID)
            ->where('paid_on', null)
            ->get();
        if (count($pendingInvoices) <=0)
            return 0;

        $pendingAmount  = 0;
        foreach ($pendingInvoices as $invoice) {
            $pendingAmount = $pendingAmount + $this->pendingAmount($invoice->id);
        }
        return $pendingAmount;
    }

    public function invoiceData($invoiceID)
    {
        $invoice = $this->model->where('id', $invoiceID)->get();
        $invoice = InvoiceResource::collection($invoice);
        $invoice->map(function($item) {
            $item['amount_paid'] =  $this->transactionRepository->invoicePaidAmount($item['id']);
            return $item;
        });
        $invoice = $invoice[0];
        $invoice = InvoiceResource::make($invoice)->resolve();

        $settings = GeneralSetting::first();
        $leaseSettings = LeaseSetting::first();
        $file_path = $settings->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;

        $settings->logo_url = $local_path;
        $settings->invoice_footer = $leaseSettings->invoice_footer;

        return [
            'invoice' => $invoice,
            'settings' => $settings,
            'local_path' => $local_path
        ];
    }
}
