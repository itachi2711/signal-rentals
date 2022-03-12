<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TenantTypeRequest;
use App\Http\Resources\TenantTypeResource;
use App\Models\Tenant;
use App\Rental\Repositories\Contracts\TenantTypeInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TenantTypeController extends ApiController
{
    /**
     * @var TenantTypeInterface
     */
    protected $tenantTypeRepository, $load, $accountRepository;

    /**
     * TenantTypeController constructor.
     * @param TenantTypeInterface $tenantTypeInterface
     */
    public function __construct(TenantTypeInterface $tenantTypeInterface)
    {
        $this->tenantTypeRepository = $tenantTypeInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->tenantTypeRepository->listAll($this->formatFields($select));
        } else
            $data = TenantTypeResource::collection($this->tenantTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param TenantTypeRequest $request
     * @return array|mixed
     */
    public function store(TenantTypeRequest $request)
    {
        $data = $request->all();
        $save = $this->tenantTypeRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! TenantType has been created.');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $tenantType = $this->tenantTypeRepository->getById($id);
        if (!$tenantType) {
            return $this->respondNotFound('TenantType not found.');
        }
        return $this->respondWithData(new TenantTypeResource($tenantType));
    }

    /**
     * Update the specified resource in storage.
     * @param TenantTypeRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(TenantTypeRequest $request, $id)
    {
        $save = $this->tenantTypeRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! TenantType has been updated.');
    }

    /**
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if (auth()->user()->tokenCan('manage-setting')) {
                $tenantType = $this->tenantTypeRepository->getById($id);
                if (!isset($tenantType))
                    throw new \Exception('TenantType not found.');

                $tenant = Tenant::where('tenant_type_id', $id)->first();
                if (isset($tenant))
                    throw new \Exception('TenantType has active tenants');

                $tenantType->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! TenantType has been deleted.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}



