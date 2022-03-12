<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/6/2021
 * Time: 7:43 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Rental\Repositories\Contracts\RoleInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends ApiController
{
    /**
     * @var RoleInterface
     */
    protected $roleRepository, $load;

    /**
     * RoleController constructor.
     * @param RoleInterface $roleInterface
     */
    public function __construct(RoleInterface $roleInterface)
    {
        $this->roleRepository = $roleInterface;
        $this->load = ['permissions'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->roleRepository->listAll($this->formatFields($select));
        } else
            $data = RoleResource::collection($this->roleRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param RoleRequest $request
     * @return array
     */
    public function store(RoleRequest $request)
    {
        $data = $request->json()->all();
        $role = $this->roleRepository->create($data);
        if ($role && array_key_exists('permission', $data)) {
            $permissions = $data['permission'];
            if (!is_null($permissions)) {
                $role->permissions()->attach($permissions);
            }
            return $this->respondWithSuccess('Success !! Role has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $role = $this->roleRepository->getById($uuid, $this->load);
        if (!$role) {
            return $this->respondNotFound('Role not found.');
        }
        return $this->respondWithData(new RoleResource($role));
    }

    /**
     * @param RoleRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(RoleRequest $request, $uuid)
    {
        $data = $request->json()->all();
        if (array_key_exists('permissions', $data)) {
            $permissions = $data['permissions'];

            if (!is_null($permissions)) {
                $this->roleRepository->getById($uuid)->permissions()->sync($permissions);
            }
        }
        $this->roleRepository->update($request->all(), $uuid);
        return $this->respondWithSuccess('Success !! Role has been updated.');
    }

    /**
     * @param $uuid
     * @return array
     * @throws \Exception
     */
    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();
            if (auth('api')->check() && auth()->user()->tokenCan('manage-setting')) {
                $user = auth()->user();
                $role = Role::with(['permissions', 'users'])->find($uuid);
                if (isset($role)) {
                    if (count($role->users) > 0) {
                        throw new \Exception('Cannot delete. Role has active users.');
                    } else {
                        $role->permissions()->detach();
                        $role->delete();
                        DB::commit();
                        return $this->respondWithSuccess('Success !! Role has been deleted.');
                    }
                }
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
