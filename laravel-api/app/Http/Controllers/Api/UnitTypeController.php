<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UnitTypeRequest;
use App\Http\Resources\UnitTypeResource;
use App\Models\Unit;
use App\Rental\Repositories\Contracts\UnitTypeInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UnitTypeController extends ApiController
{
    /**
     * @var UnitTypeInterface
     */
    protected $unitTypeRepository, $load, $accountRepository;

    /**
     * UnitTypeController constructor.
     * @param UnitTypeInterface $unitTypeInterface
     */
    public function __construct(UnitTypeInterface $unitTypeInterface)
    {
        $this->unitTypeRepository = $unitTypeInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->unitTypeRepository->listAll($this->formatFields($select));
        } else
            $data = UnitTypeResource::collection($this->unitTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param UnitTypeRequest $request
     * @return array|mixed
     */
    public function store(UnitTypeRequest $request)
    {
        $data = $request->all();
        $save = $this->unitTypeRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! UnitType has been created.');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $unitType = $this->unitTypeRepository->getById($id);
        if (!$unitType) {
            return $this->respondNotFound('Error! UnitType not found.');
        }
        return $this->respondWithData(new UnitTypeResource($unitType));
    }

    /**
     * Update the specified resource in storage.
     * @param UnitTypeRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(UnitTypeRequest $request, $id)
    {
        $save = $this->unitTypeRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! UnitType has been updated.');
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
                $unitType = $this->unitTypeRepository->getById($id);
                if (!isset($unitType))
                    throw new \Exception('Error! UnitType not found.');

                $unit = Unit::where('unit_type_id', $id)->first();
                if (isset($unit))
                    throw new \Exception('Error! UnitType has active unit');

                $unitType->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! UnitType has been deleted.');
            }
            throw new \Exception('Error! Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}


