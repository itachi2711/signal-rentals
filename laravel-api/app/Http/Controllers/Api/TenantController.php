<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TenantRequest;
use App\Http\Resources\TenantResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\TenantInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantController extends ApiController
{
    /**
     * @var TenantInterface
     */
    protected $tenantRepository, $load, $leaseRepository, $invoiceRepository;

    /**
     * TenantController constructor.
     * @param TenantInterface $tenantInterface
     * @param InvoiceInterface $invoiceRepository
     * @param LeaseInterface $leaseRepository
     */
    public function __construct(TenantInterface $tenantInterface,
                                InvoiceInterface $invoiceRepository,
                                LeaseInterface $leaseRepository)
    {
        $this->tenantRepository = $tenantInterface;
        $this->leaseRepository = $leaseRepository;
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
            return $this->tenantRepository->listAll($this->formatFields($select), ['leases']);
        } else
            $data = TenantResource::collection($this->tenantRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param TenantRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(TenantRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $tenant = $this->tenantRepository->create($data);
            if (!isset($tenant))
                return $this->respondNotSaved('Not Saved');
            DB::commit();
            CommunicationMessage::send(NEW_TENANT, $tenant);
            return $this->respondWithSuccess('Success !! Tenant has been created.');
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
        $tenant = $this->tenantRepository->getById($uuid, []);
        if (!$tenant) {
            return $this->respondNotFound('Tenant not found.');
        }
        return $this->respondWithData(new TenantResource($tenant));
    }

    /**
     * @param TenantRequest $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function update(TenantRequest $request, $id)
    {
        if (!auth()->user()->tokenCan('edit-tenant'))
            throw new \Exception('Action is not allowed.');
        try {
            DB::beginTransaction();
            $doNotUpdate = [
                'confirmed' => 1,
                'password_set' => 1,
            ];
            $data = array_diff_key($request->all(), $doNotUpdate);
            $this->tenantRepository->update(array_filter($data), $id);
            DB::commit();
            return $this->respondWithSuccess('Success !! Tenant has been updated.');
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
            if (auth()->user()->tokenCan('delete-tenant')) {
                $tenant = $this->tenantRepository->getById($uuid);
                if (!isset($tenant))
                    throw new \Exception('Tenant not found.');

                $leases = $tenant->leases;
                $pendingAmount = 0;
                foreach ($leases as $lease) {
                    $pendingAmount = $pendingAmount + $this->invoiceRepository->pendingLeaseAmount($lease->id);
                }
                if ($pendingAmount != 0)
                    throw new \Exception('Tenant has pending invoices');

                $tenant->leases()->delete();
                $tenant->payments()->delete();
                $tenant->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Tenant has been deleted.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
	public function search(Request $request) {
        $data = $request->all();
        $filter = '';
        if (array_key_exists('filter', $data))
            $filter = $data['filter'];
        return TenantResource::collection($this->tenantRepository->search($filter, ['leases']));
    }
}

