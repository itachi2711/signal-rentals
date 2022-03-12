<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/15/2021
 * Time: 4:42 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskCategoryRequest;
use App\Http\Resources\TaskCategoryResource;
use App\Rental\Repositories\Contracts\TaskCategoryInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use Illuminate\Http\Response;

class TaskCategoryController extends ApiController
{
    /**
     * @var TaskCategoryInterface
     */
    protected $taskCategoryRepository, $load, $accountRepository, $unitRepository;

    /**
     * TaskCategoryController constructor.
     * @param TaskCategoryInterface $taskCategoryInterface
     * @param UnitInterface $unitRepository
     */
    public function __construct(TaskCategoryInterface $taskCategoryInterface, UnitInterface $unitRepository)
    {
        $this->taskCategoryRepository = $taskCategoryInterface;
        $this->unitRepository = $unitRepository;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->taskCategoryRepository->listAll($this->formatFields($select), []);
        } else
            $data = TaskCategoryResource::collection($this->taskCategoryRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param TaskCategoryRequest $request
     * @return array|mixed
     */
    public function store(TaskCategoryRequest $request)
    {
        $data = $request->all();

        $save = $this->taskCategoryRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            // New member email / sms
            if (!is_null($save)) {
                // CommunicationMessage::send('new_member_welcome', $save, $save);
            }
            return $this->respondWithSuccess('Success !! TaskCategory has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $taskCategory = $this->taskCategoryRepository->getById($uuid);
        if (!$taskCategory) {
            return $this->respondNotFound('TaskCategory not found.');
        }
        return $this->respondWithData(new TaskCategoryResource($taskCategory));
    }

    /**
     * Update the specified resource in storage.
     * @param TaskCategoryRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(TaskCategoryRequest $request, $id)
    {
        $save = $this->taskCategoryRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! TaskCategory has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->taskCategoryRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! TaskCategory has been updated.');
    }
}


