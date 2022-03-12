<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LandlordRequest;
use App\Http\Resources\LandlordResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandlordController extends ApiController
{
    /**
     * @var LandlordInterface
     */
    protected $landlordRepository, $load, $accountRepository, $lease, $invoiceRepository;

    /**
     * LandlordController constructor.
     * @param LandlordInterface $landlordInterface
     * @param LeaseInterface $lease
     * @param InvoiceInterface $invoiceRepository
     */
    public function __construct(LandlordInterface $landlordInterface,
                                LeaseInterface $lease,
                                InvoiceInterface $invoiceRepository)
    {
        $this->landlordRepository   = $landlordInterface;
        $this->invoiceRepository = $invoiceRepository;
        $this->lease   = $lease;
        $this->load = [];
    }

    /**
     * @return mixed
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->landlordRepository->listAll($this->formatFields($select), []);
        } else
            $data = LandlordResource::collection($this->landlordRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param LandlordRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(LandlordRequest $request)
    {
        try {
            DB::beginTransaction();
                $data = $request->all();
                $landlord = $this->landlordRepository->create($data);
                if (!isset($landlord))
                    return $this->respondNotSaved('Not Saved');
            DB::commit();
            CommunicationMessage::send(NEW_LANDLORD, $landlord);
            return $this->respondWithSuccess('Success !! Landlord has been created.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $landlord = $this->landlordRepository->getById($uuid);

        if (!$landlord) {
            return $this->respondNotFound('Landlord not found.');
        }

        /*$invoice->map(function($item) {
            $item['amount_paid'] =  $this->transactionRepository->invoicePaidAmount($item['id']);
            return $item;
        });*/
		$landlordResource = LandlordResource::make($landlord);
		// $invoiceResource['amount_paid']=  $this->transactionRepository->invoicePaidAmount($invoiceResource['id']);

        return $this->respondWithData($landlordResource);
    }

    /**
     * @param LandlordRequest $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function update(LandlordRequest $request, $id)
    {
        if (!auth()->user()->tokenCan('edit-landlord'))
            throw new \Exception('Action is not allowed.');
        try {
            DB::beginTransaction();
            $doNotUpdate = [
                'confirmed' => 1,
                'password_set' => 1,
            ];
            $data = array_diff_key($request->all(), $doNotUpdate);
            $this->landlordRepository->update(array_filter($data), $id);
            DB::commit();
            return $this->respondWithSuccess('Success !! Landlord has been updated.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $uuid
     * @return array
     * @throws \Exception
     */
    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();
            if (auth()->user()->tokenCan('delete-landlord')) {
                $landlord = $this->landlordRepository->getById($uuid);
                if (!isset($landlord))
                    throw new \Exception('Landlord not found.');

                $leases = $landlord->leases;
                $pendingAmount = 0;
                foreach ($leases as $lease) {
                    $pendingAmount = $pendingAmount + $this->invoiceRepository->pendingLeaseAmount($lease->id);
                }
                if ($pendingAmount != 0)
                    throw new \Exception('Landlord has pending invoices');
                $landlord->properties()->each(function($property) {
                    $property->extra_charges()->detach();
                    $property->notices()->delete();
                    $property->invoices()->delete();
                    $property->periods()->detach();
                    $property->payment_methods()->detach();
                    $property->late_fees()->detach();
                    $property->extra_charges()->detach();
                    $property->leases()->delete();
                    $property->units()->delete();
                });
                $landlord->properties()->delete();
                $landlord->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Landlord has been deleted.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request) {
        $data = $request->all();
        $filter = '';
        if (array_key_exists('filter', $data))
            $filter = $data['filter'];
        return $this->landlordRepository->search($filter);
    }
}
