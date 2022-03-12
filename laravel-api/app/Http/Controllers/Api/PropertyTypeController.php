<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PropertyTypeRequest;
use App\Http\Resources\PropertyTypeResource;
use App\Models\Lease;
use App\Models\Property;
use App\Rental\Repositories\Contracts\PropertyTypeInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PropertyTypeController extends ApiController
{
    /**
     * @var PropertyTypeInterface
     */
    protected $propertyTypeRepository, $load, $accountRepository;

    /**
     * PropertyTypeController constructor.
     * @param PropertyTypeInterface $propertyTypeInterface
     */
    public function __construct(PropertyTypeInterface $propertyTypeInterface)
    {
        $this->propertyTypeRepository = $propertyTypeInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->propertyTypeRepository->listAll($this->formatFields($select));
        } else
            $data = PropertyTypeResource::collection($this->propertyTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param PropertyTypeRequest $request
     * @return array|mixed
     */
    public function store(PropertyTypeRequest $request)
    {
        $data = $request->all();
        $save = $this->propertyTypeRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! PropertyType has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param PropertyTypeRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(PropertyTypeRequest $request, $id)
    {
        $save = $this->propertyTypeRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! PropertyType has been updated.');
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
                $propertyType = $this->propertyTypeRepository->getById($id);
                if (!isset($propertyType))
                    throw new \Exception('Error! PropertyType not found.');

                $property = Property::where('property_type_id', $id)->first();
                if (isset($property))
                    throw new \Exception('Error! PropertyType has active properties');

                $propertyType->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! PropertyType has been deleted.');
            }
            throw new \Exception('Error! Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}


