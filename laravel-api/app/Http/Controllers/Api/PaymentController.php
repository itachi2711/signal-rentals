<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:16 PM
 */

namespace App\Http\Controllers\Api;

use App\Events\PaymentReceived;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\PaymentStatusRequest;
use App\Http\Resources\PaymentResource;
use App\Models\GeneralSetting;
use App\Rental\Repositories\Contracts\PaymentInterface;
use App\Rental\Repositories\Contracts\TenantInterface;
use App\Traits\CommunicationMessage;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends ApiController
{
    protected $paymentRepository, $load, $tenantRepository;

    /**
     * PaymentController constructor.
     * @param PaymentInterface $paymentInterface
     * @param TenantInterface $tenantRepository
     */
    public function __construct(PaymentInterface $paymentInterface, TenantInterface $tenantRepository)
    {
        $this->paymentRepository = $paymentInterface;
        $this->tenantRepository = $tenantRepository;
        $this->load = ['payment_method', 'cancel_user', 'approve_user'];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->paymentRepository->listAll($this->formatFields($select), []);
        } else
            $data = PaymentResource::collection($this->paymentRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param PaymentRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(PaymentRequest $request)
    {
        if (!auth()->user()->tokenCan('create-payment'))
            throw new \Exception('Action is not allowed.');

        try {
            DB::beginTransaction();
            $newPayment = $this->paymentRepository->create($request->validated());
            if (!isset($newPayment)) {
                return $this->respondNotSaved('Not Saved');
            }
                DB::commit();
                return $this->respondWithSuccess('Success !! Payment has been created.');
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
        $payment = $this->paymentRepository->getById($uuid);

        if (!$payment) {
            return $this->respondNotFound('Payment not found.');
        }
        return $this->respondWithData(new PaymentResource($payment));
    }

    /**
     * @param PaymentStatusRequest $request
     * @throws \Exception
     */
    public function approve(PaymentStatusRequest $request) {
        if (!auth()->user()->tokenCan('approve-payment'))
            throw new \Exception('Action is not allowed.');

        try
        {
            DB::beginTransaction();
            $data = $request->all();
            $data['payment_status'] = 'approved';
            if (auth()->user()) {
                $data['approved_by'] = auth()->user()->id;
            }
            $payment = $this->paymentRepository->getById($data['id']);
            $this->paymentRepository->update(array_filter($data), $data['id']);
            if(isset($payment))
                event(new PaymentReceived($payment));
            DB::commit();
            $tenant = $this->tenantRepository->getByID($payment['tenant_id']);
            CommunicationMessage::send(RECEIVE_PAYMENT, $tenant, $payment);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param PaymentStatusRequest $request
     * @throws \Exception
     */
    public function cancel(PaymentStatusRequest $request) {
        if (!auth()->user()->tokenCan('cancel-payment'))
            throw new \Exception('Action is not allowed.');

        try
        {
            DB::beginTransaction();
            $data = $request->all();
            $data['payment_status'] = 'cancelled';
            if (auth()->user()){
                $data['cancelled_by'] = auth()->user()->id;
            }
            $this->paymentRepository->update(array_filter($data), $data['id']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param PaymentStatusRequest $request
     */
    public function pending(PaymentStatusRequest $request) {
        $data = $request->all();
        $data['payment_status'] = 'pending';
        $this->paymentRepository->update(array_filter($data), $data['id']);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function downloadReceipt(Request $request) {
        $data = $request->all();
        $uuid = $data['id'];

        $paymentID = $data['id'];
        $payment = PaymentResource::make($this->paymentRepository->getById($paymentID));

/*        $invoice = Invoice::where('id', $uuid)->get();
        $invoice = InvoiceResource::collection($invoice);
        $invoice->map(function($item) {
            $item['amount_paid'] =  $this->transactionRepository->invoicePaidAmount($item['id']);
            return $item;
        });
        $invoice = $invoice[0];
        $invoice = InvoiceResource::make($invoice)->toArray($request);*/

        $settings = GeneralSetting::first();
        $file_path = $settings->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;

        $settings->logo_url = $local_path;

        $pdf = PDF::loadView('invoices.receipt', compact('payment', 'settings'), compact('local_path'));
        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf->getDomPDF()->set_option("enable_php", true);

        // return view('invoices.invoice', compact('invoice'), compact('local_path'));
        return $pdf->download('receipt.pdf');
    }
}
