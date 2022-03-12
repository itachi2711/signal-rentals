<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/6/2021
 * Time: 7:43 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Rental\Repositories\Contracts\PermissionInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends ApiController
{
    /**
     * @var PermissionInterface
     */
    protected $permissionRepository;

    /**
     * PermissionController constructor.
     * @param PermissionInterface $permissionInterface
     */
    public function __construct(PermissionInterface $permissionInterface)
    {
        $this->permissionRepository = $permissionInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->permissionRepository->listAll($this->formatFields($select));
        } else
            $data = PermissionResource::collection($this->permissionRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param PermissionRequest $request
     * @return mixed
     */
    public function store(PermissionRequest $request)
    {
        return $this->respondNotSaved('Not allowed');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $permission = $this->permissionRepository->getById($uuid);
        if (!$permission) {
            return $this->respondNotFound('Permission not found.');
        }
        return $this->respondWithData(new PermissionResource($permission));
    }

    /**
     * @param PermissionRequest $request
     * @param $uuid
     * @return array
     * @throws \Exception
     */
    public function update(PermissionRequest $request, $uuid)
    {
        try {
            DB::beginTransaction();
            $doNotUpdate = [
                'name' => 1
            ];
            $data = array_diff_key($request->validated(), $doNotUpdate);
            $this->permissionRepository->update(array_filter($data), $uuid);
            DB::commit();
            return $this->respondWithSuccess('Success !! Permission has been updated.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        return $this->respondNotSaved('Not allowed');
    }
}
