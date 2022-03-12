<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UtilityRequest;
use App\Http\Resources\UtilityResource;
use App\Models\LeaseUtilityCharge;
use App\Models\PropertyUtilityCost;
use App\Rental\Repositories\Contracts\UtilityInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UtilityController extends ApiController
{
    /**
     * @var UtilityInterface
     */
    protected $utilityRepository, $load, $accountRepository;

    /**
     * UtilityController constructor.
     * @param UtilityInterface $utilityInterface
     */
    public function __construct(UtilityInterface $utilityInterface)
    {
        $this->utilityRepository = $utilityInterface;
		$this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->utilityRepository->listAll($this->formatFields($select));
        } else
            $data = UtilityResource::collection($this->utilityRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param UtilityRequest $request
     * @return array|mixed
     */
    public function store(UtilityRequest $request)
    {
        $data = $request->all();
        $save = $this->utilityRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Utility has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param UtilityRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(UtilityRequest $request, $id)
    {
        $save = $this->utilityRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Utility has been updated.');
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
                $utility = $this->utilityRepository->getById($id);
                if (!isset($utility))
                    throw new \Exception('Error! Utility not found.');

                $leaseUtility = LeaseUtilityCharge::where('utility_id', $id)->get();
                $propertyUtility = PropertyUtilityCost::where('utility_id', $id)->get();

                if (count($leaseUtility) > 0 || count($propertyUtility) > 0)
                    throw new \Exception('Error! Utility has active lease or property');

                $utility->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Utility has been deleted.');
            }
            throw new \Exception('Error! Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}


