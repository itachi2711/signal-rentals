<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/30/2021
 * Time: 5:10 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\WaiverRequest;
use App\Http\Resources\WaiverResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\WaiverInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WaiverController extends ApiController
{
    /**
     * @var WaiverInterface
     */
    protected $waiverRepository, $load, $accountRepository, $invoiceRepository;

    /**
     * WaiverController constructor.
     * @param WaiverInterface $waiverInterface
     * @param InvoiceInterface $invoiceRepository
     */
    public function __construct(WaiverInterface $waiverInterface, InvoiceInterface $invoiceRepository)
    {
        $this->waiverRepository = $waiverInterface;
         $this->invoiceRepository = $invoiceRepository;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->waiverRepository->listAll($this->formatFields($select));
        } else
            $data = WaiverResource::collection($this->waiverRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param WaiverRequest $request
     * @throws \Exception
     */
    public function store(WaiverRequest $request)
    {
        if (!auth()->user()->tokenCan('waive-invoice'))
            throw new \Exception('Action is not allowed.');

        $data = $request->all();
        $newWaiver = $this->waiverRepository->create($data);

        try {
            DB::beginTransaction();
            $invoiceID = $newWaiver['invoice_id'];
            $waiveAmount = $newWaiver['amount'];

            $invoice = $this->invoiceRepository->getById($invoiceID);
            if (!isset($invoice))
                throw new \Exception('Invoice not found');

            if (isset($invoice) && $invoice['paid_on'] != null)
                throw new \Exception('Invoice is already paid.');

            $pendingAmount = $this->invoiceRepository->pendingAmount($invoiceID);
            if($pendingAmount == 0)
                throw new \Exception('Invoice is already cleared.');

            if($waiveAmount > $pendingAmount)
                throw new \Exception('Waiver amount must not be more than invoice pending amount.');

            $this->waiverRepository->processWaiver($newWaiver);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            throw new \Exception($exception->getMessage());
        }
    }
}


