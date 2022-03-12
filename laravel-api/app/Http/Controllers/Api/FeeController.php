<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FeeRequest;
use App\Http\Resources\FeeResource;
use App\Rental\Repositories\Contracts\FeeInterface;
use Facade\FlareClient\Http\Response;

class FeeController extends ApiController
{
    /**
     * @var FeeInterface
     */
    protected $feeRepository, $load, $accountRepository;

    /**
     * FeeController constructor.
     * @param FeeInterface $feeInterface
     */
    public function __construct(FeeInterface $feeInterface)
    {
        $this->feeRepository = $feeInterface;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->feeRepository->listAll($this->formatFields($select));
        } else
            $data = FeeResource::collection($this->feeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param FeeRequest $request
     * @return array|mixed
     */
    public function store(FeeRequest $request)
    {
        $data = $request->all();


        $save = $this->feeRepository->create($data);


        return $save;


        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            // New member email / sms
            if (!is_null($save)) {
                // CommunicationMessage::send('new_member_welcome', $save, $save);
            }
            return $this->respondWithSuccess('Success !! Fee has been created.');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('fee::show');
    }

    /**
     * Update the specified resource in storage.
     * @param FeeRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(FeeRequest $request, $id)
    {
        $save = $this->feeRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Fee has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->feeRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Fee has been updated.');
    }
}



