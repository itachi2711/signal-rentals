<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 5/28/2021
 * Time: 7:51 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Rental\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    /**
     * @var UserInterface
     */
    protected $userRepository, $load;

    /**
     * UserController constructor.
     * @param UserInterface $userInterface
     */
    public function __construct(UserInterface $userInterface)
    {
        $this->userRepository = $userInterface;
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
            return $this->userRepository->listAll($this->formatFields($select), []);
        } else
            $data = UserResource::collection($this->userRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param UserRequest $request
     * @return mixed
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();
        $save = $this->userRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            //TODO:  New user email / sms
            return $this->respondWithSuccess('Success !! User has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $user = $this->userRepository->getById($uuid);

        if (!$user) {
            return $this->respondNotFound('User not found.');
        }
        return $this->respondWithData(new UserResource($user));

    }

    /**
     * @param UserRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(UserRequest $request, $uuid)
    {
        $save = $this->userRepository->update(array_filter($request->all()), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
          //  $this->loginProxy->logout();
            return $this->respondWithSuccess('Success !! User has been updated.');
        }
    }

    /**
     * @param $uuid
     * @return array|mixed
     * @throws \Exception
     */
    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();
            if (auth('api')->check() && auth()->user()->tokenCan('manage-setting')) {
                $userID = auth()->user()->id;
                if ($uuid != $userID) {
                    $this->userRepository->delete($uuid);
                    DB::commit();
                    return $this->respondWithSuccess('Success !! Landlord has been deleted.');
                }
                else
                    throw new \Exception('Error: Cannot delete self.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
