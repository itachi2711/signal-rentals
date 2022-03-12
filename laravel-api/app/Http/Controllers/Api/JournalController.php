<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\JournalRequest;
use App\Http\Resources\JournalResource;
use App\Rental\Repositories\Contracts\JournalInterface;
use Illuminate\Http\Request;

class JournalController extends ApiController
{
    /**
     * @var JournalInterface
     */
    protected $journalRepository, $load;

    /**
     * JournalController constructor.
     * @param JournalInterface $journalInterface
     */
    public function __construct(JournalInterface $journalInterface)
    {
        $this->journalRepository = $journalInterface;
        $this->load = ['debitAccount', 'creditAccount', 'preparedBy'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->journalRepository->listAll($this->formatFields($select));
        } else
            $data = JournalResource::collection($this->journalRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param JournalRequest $request
     * @return mixed
     */
    public function store(JournalRequest $request)
    {
        $save = $this->journalRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Journal has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $journal = $this->journalRepository->getById($uuid);

        if (!$journal) {
            return $this->respondNotFound('Journal not found.');
        }
        return $this->respondWithData(new JournalResource($journal));
    }

    /**
     * @param JournalRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(JournalRequest $request, $uuid)
    {
        $save = $this->journalRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Journal has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->journalRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Journal has been deleted');
        }
        return $this->respondNotFound('Journal not deleted');
    }
}
