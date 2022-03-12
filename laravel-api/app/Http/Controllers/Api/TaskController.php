<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Rental\Repositories\Contracts\TaskInterface;
use Illuminate\Http\Response;

class TaskController extends ApiController
{
    /**
     * @var TaskInterface
     */
    protected $taskRepository, $load, $accountRepository;

    /**
     * TaskController constructor.
     * @param TaskInterface $taskInterface
     */
    public function __construct(TaskInterface $taskInterface)
    {
        $this->taskRepository = $taskInterface;
        $this->load = ['permissions'];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->taskRepository->listAll($this->formatFields($select));
        } else
            $data = TaskResource::collection($this->taskRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param TaskRequest $request
     * @return array
     */
    public function store(TaskRequest $request)
    {
        $data = $request->json()->all();

        $task = $this->taskRepository->create($data);

        if ($task && array_key_exists('permission', $data)) {
            $permissions = $data['permission'];
            if (!is_null($permissions)) {
                $task->permissions()->attach($permissions);
            }
            return $this->respondWithSuccess('Success !! Task has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param TaskRequest $request
     * @param $uuid
     * @return array|mixed
     */
    public function update(TaskRequest $request, $uuid)
    {
        $data = $request->json()->all();
        if (array_key_exists('permissions', $data)) {
            $permissions = $data['permissions'];

            if (!is_null($permissions)) {
                $this->taskRepository->getById($uuid)->permissions()->sync($permissions);
            }
        }
        $this->taskRepository->update($request->all(), $uuid);
        return $this->respondWithSuccess('Success !! Task has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->taskRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Task has been updated.');
    }
}
