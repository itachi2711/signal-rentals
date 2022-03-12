<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UnitRequest;
use App\Http\Resources\UnitResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UnitController extends ApiController
{
    /**
     * @var UnitInterface
     */
    protected $unitRepository, $load, $accountRepository, $invoiceRepository;

    /**
     * UnitController constructor.
     * @param UnitInterface $unitInterface
     * @param InvoiceInterface $invoiceRepository
     */
    public function __construct(UnitInterface $unitInterface, InvoiceInterface $invoiceRepository)
    {
        $this->unitRepository = $unitInterface;
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
            return $this->unitRepository->listAll($this->formatFields($select));
        } else
            $data = UnitResource::collection($this->unitRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param UnitRequest $request
     * @return array|mixed
     */
    public function store(UnitRequest $request)
    {
        $data = $request->all();
        $save = $this->unitRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Unit has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param UnitRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(UnitRequest $request, $id)
    {
        $data = $request->all();
        $save = $this->unitRepository->update($request->validated(), $id);

    /*    // save amenities to pivot
        if(array_key_exists('selected_amenities', $data)){
            $selectedAmenitiesData = $unit['selected_amenities'];
            if (isset($selectedAmenitiesData)){
                foreach ($selectedAmenitiesData as $amenityKey => $amenity){
                    $newUnit->amenities()->attach($amenity);
                }
            }
        }

        // save utilities to pivot
        if(array_key_exists('selected_utilities', $data)){
            $selectedUtilitiesData = $unit['selected_utilities'];
            if (isset($selectedUtilitiesData)){
                foreach ($selectedUtilitiesData as $UtilityKey => $utility){
                    $newUnit->utilities()->attach($utility);
                }
            }
        }*/

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Unit has been updated.');
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
            if (auth()->user()->tokenCan('delete-property')) {
                $unit = $this->unitRepository->getById($uuid);
                if (!isset($unit))
                    throw new \Exception('Unit not found.');

                $leases = $unit->leases;
                $pendingAmount = 0;
                foreach ($leases as $lease) {
                    $pendingAmount = $pendingAmount + $this->invoiceRepository->pendingLeaseAmount($lease->id);
                }
                if ($pendingAmount != 0)
                    throw new \Exception('Unit has pending invoices');

                $unit->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Unit has been deleted.');
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
    public function vacantUnits(Request $request)
    {
        $data = $this->unitRepository->getVacantUnits(['leases']);
        return UnitResource::collection($data);
    }
}

