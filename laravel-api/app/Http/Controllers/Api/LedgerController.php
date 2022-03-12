<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:16 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LedgerRequest;
use App\Http\Resources\LedgerResource;
use App\Rental\Repositories\Contracts\LedgerInterface;
use Illuminate\Http\Request;

class LedgerController extends ApiController
{
    /**
     * @var LedgerInterface
     */
    protected $ledgerRepository, $load;

    /**
     * LedgerController constructor.
     * @param LedgerInterface $ledgerInterface
     */
    public function __construct(LedgerInterface $ledgerInterface)
    {
        $this->ledgerRepository = $ledgerInterface;
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
            return $this->ledgerRepository->listAll($this->formatFields($select));
        } else
            $data = LedgerResource::collection($this->ledgerRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param LedgerRequest $request
     * @return mixed
     */
    public function store(LedgerRequest $request)
    {
        $save = $this->ledgerRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Ledger has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $ledger = $this->ledgerRepository->getById($uuid);

        if (!$ledger) {
            return $this->respondNotFound('Ledger not found.');
        }
        return $this->respondWithData(new LedgerResource($ledger));
    }

    /**
     * @param LedgerRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LedgerRequest $request, $uuid)
    {
        $save = $this->ledgerRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Ledger has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->ledgerRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Ledger has been deleted');
        }
        return $this->respondNotFound('Ledger not deleted');
    }
}

