<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LeaseModeRequest;
use App\Http\Resources\LeaseModeResource;
use App\Rental\Repositories\Contracts\LeaseModeInterface;
use Illuminate\Http\Response;

class LeaseModeController extends ApiController
{
    /**
     * @var LeaseModeInterface
     */
    protected $leaseModeRepository, $load, $accountRepository;

    /**
     * LeaseModeController constructor.
     * @param LeaseModeInterface $leaseModeInterface
     */
    public function __construct(LeaseModeInterface $leaseModeInterface)
    {
        $this->leaseModeRepository = $leaseModeInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->leaseModeRepository->listAll($this->formatFields($select));
        } else
            $data = LeaseModeResource::collection($this->leaseModeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param LeaseModeRequest $request
     * @return array|mixed
     */
    public function store(LeaseModeRequest $request)
    {
        $data = $request->all();


        $save = $this->leaseModeRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            // New member email / sms
            if (!is_null($save)) {
                // CommunicationMessage::send('new_member_welcome', $save, $save);
            }
            return $this->respondWithSuccess('Success !! LeaseMode has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param LeaseModeRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(LeaseModeRequest $request, $id)
    {
        $save = $this->leaseModeRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! LeaseMode has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->leaseModeRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! LeaseMode has been updated.');
    }
}




