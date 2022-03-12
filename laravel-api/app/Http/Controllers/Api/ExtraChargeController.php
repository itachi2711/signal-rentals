<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExtraChargeRequest;
use App\Http\Resources\ExtraChargeResource;
use App\Rental\Repositories\Contracts\ExtraChargeInterface;
use Illuminate\Http\Request;

class ExtraChargeController extends ApiController
{
    /**
     * @var ExtraChargeInterface
     */
    protected $extraChargeRepository, $load;

    /**
     * ExtraChargeController constructor.
     * @param ExtraChargeInterface $extraChargeInterface
     */
    public function __construct(ExtraChargeInterface $extraChargeInterface)
    {
        $this->extraChargeRepository = $extraChargeInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->extraChargeRepository->listAll($this->formatFields($select));
        } else
            $data = ExtraChargeResource::collection($this->extraChargeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param ExtraChargeRequest $request
     * @return mixed
     */
    public function store(ExtraChargeRequest $request)
    {
        $save = $this->extraChargeRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! ExtraCharge has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $extraCharge = $this->extraChargeRepository->getById($uuid);

        if (!$extraCharge) {
            return $this->respondNotFound('ExtraCharge not found.');
        }
        return $this->respondWithData(new ExtraChargeResource($extraCharge));
    }

    /**
     * @param ExtraChargeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(ExtraChargeRequest $request, $uuid)
    {
        $save = $this->extraChargeRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! ExtraCharge has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->extraChargeRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! ExtraCharge has been deleted');
        }
        return $this->respondNotFound('ExtraCharge not deleted');
    }
}

