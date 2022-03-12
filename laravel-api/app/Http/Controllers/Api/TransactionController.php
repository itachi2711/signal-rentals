<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/30/2021
 * Time: 5:00 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Rental\Repositories\Contracts\TransactionInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends ApiController
{
    /**
     * @var TransactionInterface
     */
    protected $transactionRepository, $load, $accountRepository;

    /**
     * TransactionController constructor.
     * @param TransactionInterface $transactionInterface
     */
    public function __construct(TransactionInterface $transactionInterface)
    {
        $this->transactionRepository = $transactionInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->transactionRepository->listAll($this->formatFields($select));
        } else
            $data = TransactionResource::collection($this->transactionRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param TransactionRequest $request
     * @return array|mixed
     */
    public function store(TransactionRequest $request)
    {
        $data = $request->all();


        $save = $this->transactionRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            // New member email / sms
            if (!is_null($save)) {
                // CommunicationMessage::send('new_member_welcome', $save, $save);
            }
            return $this->respondWithSuccess('Success !! Transaction has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param TransactionRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(TransactionRequest $request, $id)
    {
        $save = $this->transactionRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Transaction has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->transactionRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Transaction has been updated.');
    }

    public function search(Request $request) {
        $data = $request->all();
        if (array_key_exists('filter', $data)) {
            $filter = $data['filter'];
            return $this->transactionRepository->search($filter);
        }
    }
}

