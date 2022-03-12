<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/16/2021
 * Time: 7:27 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PeriodRequest;
use App\Http\Resources\PeriodResource;
use App\Rental\Repositories\Contracts\PeriodInterface;
use Illuminate\Http\Request;

class PeriodController extends ApiController
{
    /**
     * @var PeriodInterface
     */
    protected $periodRepository;

    /**
     * PeriodController constructor.
     * @param PeriodInterface $periodInterface
     */
    public function __construct(PeriodInterface $periodInterface)
    {
        $this->periodRepository = $periodInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->periodRepository->listAll($this->formatFields($select));
        } else
            $data = PeriodResource::collection($this->periodRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param PeriodRequest $request
     * @return mixed
     */
    public function store(PeriodRequest $request)
    {
        $save = $this->periodRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Period has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $period = $this->periodRepository->getById($uuid);
        if (!$period) {
            return $this->respondNotFound('Period not found.');
        }
        return $this->respondWithData(new PeriodResource($period));
    }

    /**
     * @param PeriodRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(PeriodRequest $request, $uuid)
    {
        $save = $this->periodRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Period has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->periodRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Period has been deleted');
        }
        return $this->respondNotFound('Period not deleted');
    }
}
