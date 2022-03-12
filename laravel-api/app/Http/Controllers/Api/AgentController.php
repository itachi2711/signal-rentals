<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AgentRequest;
use App\Http\Resources\AgentResource;
use App\Rental\Repositories\Contracts\AgentInterface;
use Illuminate\Http\Response;

class AgentController extends ApiController
{
    /**
     * @var AgentInterface
     */
    protected $agentRepository, $load, $accountRepository;

    /**
     * AgentController constructor.
     * @param AgentInterface $agentInterface
     */
    public function __construct(AgentInterface $agentInterface)
    {
        $this->agentRepository = $agentInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->agentRepository->listAll($this->formatFields($select));
        } else
            $data = AgentResource::collection($this->agentRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param AgentRequest $request
     * @return array|mixed
     */
    public function store(AgentRequest $request)
    {
        $data = $request->all();


        $save = $this->agentRepository->create($data);


        return $save;


        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            // New member email / sms
            if (!is_null($save)) {
                // CommunicationMessage::send('new_member_welcome', $save, $save);
            }
            return $this->respondWithSuccess('Success !! Agent has been created.');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('agent::show');
    }

    /**
     * Update the specified resource in storage.
     * @param AgentRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(AgentRequest $request, $id)
    {
        $save = $this->agentRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Agent has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->agentRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Agent has been updated.');
    }
}



