<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/27/2021
 * Time: 9:55 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LateFeeRequest;
use App\Http\Resources\LateFeeResource;
use App\Rental\Repositories\Contracts\LateFeeInterface;
use Illuminate\Http\Request;

class LateFeeController extends ApiController
{
    /**
     * @var LateFeeInterface
     */
    protected $lateFeeRepository, $load;

    /**
     * LateFeeController constructor.
     * @param LateFeeInterface $lateFeeInterface
     */
    public function __construct(LateFeeInterface $lateFeeInterface)
    {
        $this->lateFeeRepository = $lateFeeInterface;
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
            return $this->lateFeeRepository->listAll($this->formatFields($select));
        } else
            $data = LateFeeResource::collection($this->lateFeeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param LateFeeRequest $request
     * @return mixed
     */
    public function store(LateFeeRequest $request)
    {
        $save = $this->lateFeeRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! LateFee has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $lateFee = $this->lateFeeRepository->getById($uuid);

        if (!$lateFee) {
            return $this->respondNotFound('LateFee not found.');
        }
        return $this->respondWithData(new LateFeeResource($lateFee));
    }

    /**
     * @param LateFeeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LateFeeRequest $request, $uuid)
    {
        $save = $this->lateFeeRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! LateFee has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->lateFeeRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! LateFee has been deleted');
        }
        return $this->respondNotFound('LateFee not deleted');
    }
}


