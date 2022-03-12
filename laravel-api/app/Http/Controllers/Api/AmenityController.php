<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AmenityRequest;
use App\Http\Resources\AmenityResource;
use App\Rental\Repositories\Contracts\AmenityInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AmenityController extends ApiController
{
    /**
     * @var AmenityInterface
     */
    protected $amenityRepository, $load, $accountRepository;

    /**
     * AmenityController constructor.
     * @param AmenityInterface $amenityInterface
     */
    public function __construct(AmenityInterface $amenityInterface)
    {
        $this->amenityRepository = $amenityInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->amenityRepository->listAll($this->formatFields($select));
        } else
            $data = AmenityResource::collection($this->amenityRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param AmenityRequest $request
     * @return array|mixed
     */
    public function store(AmenityRequest $request)
    {
        $data = $request->all();
        $save = $this->amenityRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Amenity has been created.');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unitType = $this->amenityRepository->getById($id);
        if (!$unitType) {
            return $this->respondNotFound('Error! Amenity not found.');
        }
        return $this->respondWithData(new AmenityResource($unitType));
    }

    /**
     * Update the specified resource in storage.
     * @param AmenityRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(AmenityRequest $request, $id)
    {
        $save = $this->amenityRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Amenity has been updated.');
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
                $amenity = $this->amenityRepository->getById($id);
                if (!isset($amenity))
                    throw new \Exception('Error! Amenity not found.');
                else
                    $amenity->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Amenity has been deleted.');
            }
            throw new \Exception('Error! Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}



