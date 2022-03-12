<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:16 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LeaseTypeRequest;
use App\Http\Resources\LeaseTypeResource;
use App\Models\Lease;
use App\Models\Tenant;
use App\Rental\Repositories\Contracts\LeaseTypeInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LeaseTypeController extends ApiController
{
    /**
     * @var LeaseTypeInterface
     */
    protected $leaseTypeRepository, $load, $accountRepository;

    /**
     * LeaseTypeController constructor.
     * @param LeaseTypeInterface $leaseTypeInterface
     */
    public function __construct(LeaseTypeInterface $leaseTypeInterface)
    {
        $this->leaseTypeRepository = $leaseTypeInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->leaseTypeRepository->listAll($this->formatFields($select));
        } else
            $data = LeaseTypeResource::collection($this->leaseTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param LeaseTypeRequest $request
     * @return array|mixed
     */
    public function store(LeaseTypeRequest $request)
    {
        $data = $request->all();
        $save = $this->leaseTypeRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! LeaseType has been created.');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $leaseType = $this->leaseTypeRepository->getById($id);
        if (!$leaseType) {
            return $this->respondNotFound('Error! LeaseType not found.');
        }
        return $this->respondWithData(new LeaseTypeResource($leaseType));
    }

    /**
     * Update the specified resource in storage.
     * @param LeaseTypeRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(LeaseTypeRequest $request, $id)
    {
        $save = $this->leaseTypeRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! LeaseType has been updated.');
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
                $leaseType = $this->leaseTypeRepository->getById($id);
                if (!isset($leaseType))
                    throw new \Exception('LeaseType not found.');

                $lease = Lease::where('lease_type_id', $id)->first();
                if (isset($lease))
                    throw new \Exception('Error! LeaseType has active leases');

                $leaseType->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! LeaseType has been deleted.');
            }
            throw new \Exception('Error! Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}



