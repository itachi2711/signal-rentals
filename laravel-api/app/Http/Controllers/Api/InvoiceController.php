<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\GeneralSetting;
use App\Models\Invoice;
use App\Models\LeaseSetting;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InvoiceController extends ApiController
{
    /**
     * @var InvoiceInterface
     */
    protected $invoiceRepository, $load,
        $accountRepository, $unitRepository, $transactionRepository;

    /**
     * InvoiceController constructor.
     * @param InvoiceInterface $invoiceInterface
     * @param UnitInterface $unitRepository
     * @param TransactionInterface $transactionRepository
     */
    public function __construct(InvoiceInterface $invoiceInterface, UnitInterface $unitRepository,
                                TransactionInterface $transactionRepository)
    {
        $this->invoiceRepository = $invoiceInterface;
        $this->unitRepository = $unitRepository;
        $this->transactionRepository = $transactionRepository;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->invoiceRepository->listAll($this->formatFields($select), []);
        } else
            $data = InvoiceResource::collection($this->invoiceRepository->getAllPaginate($this->load));

        $data->map(function($item) {
            $item['amount_paid'] =  $this->transactionRepository->invoicePaidAmount($item['id']);
            return $item;
        });

        return $this->respondWithData($data);
    }

    /**
     * @param InvoiceRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(InvoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $newInvoice = $this->invoiceRepository->create($data);

            if (!isset($newInvoice)) {
                return $this->respondNotSaved('Not Saved');
            }
            DB::commit();
            return $this->respondWithSuccess('Success !! Invoice has been created.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
		$invoice = $this->invoiceRepository->getById($uuid, $this->load);
        if (!$invoice) {
            return $this->respondNotFound('Invoice not found.');
        }
		$invoiceResource = InvoiceResource::make($invoice);
		$invoiceResource['amount_paid']=  $this->transactionRepository->invoicePaidAmount($invoiceResource['id']);
        return $this->respondWithData($invoiceResource);
    }

    /**
     * @param InvoiceRequest $request
     * @param $id
     */
    public function update(InvoiceRequest $request, $id)
    {
        $save = $this->invoiceRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Invoice has been updated.');
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        return;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function downloadInvoice(Request $request) {
        $data = $request->all();
        $uuid = $data['id'];

        $invoice = Invoice::where('id', $uuid)->get();
        $invoice = InvoiceResource::collection($invoice);
        $invoice->map(function($item) {
            $item['amount_paid'] =  $this->transactionRepository->invoicePaidAmount($item['id']);
            return $item;
        });
        $invoice = $invoice[0];
        $invoice = InvoiceResource::make($invoice)->toArray($request);

        $settings = GeneralSetting::first();
        $leaseSettings = LeaseSetting::first();
        $file_path = $settings->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;

        $settings->logo_url = $local_path;
        $settings->invoice_footer = $leaseSettings->invoice_footer;

        $pdf = PDF::loadView('invoices.invoice', compact('invoice', 'settings'), compact('local_path'));
        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf->getDomPDF()->set_option("enable_php", true);

       // return view('invoices.invoice', compact('invoice'), compact('local_path'));
        return $pdf->download('invoice.pdf');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $data = $request->all();
        if (array_key_exists('filter', $data)) {
            $filter = $data['filter'];
            return $this->invoiceRepository->search($filter);
        }
        return [];
    }
}
