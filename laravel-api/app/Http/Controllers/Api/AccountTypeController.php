<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:43
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccountTypeRequest;
use App\Http\Resources\AccountTypeResource;

use App\Rental\Repositories\Contracts\AccountTypeInterface;
use Illuminate\Http\Request;

class AccountTypeController extends ApiController
{
    /**
     * @var AccountTypeInterface
     */
    protected $accountTypeRepository, $load;

    /**
     * AccountTypeController constructor.
     * @param AccountTypeInterface $accountTypeInterface
     */
    public function __construct(AccountTypeInterface $accountTypeInterface)
    {
        $this->accountTypeRepository = $accountTypeInterface;
        $this->load = ['accountClass'];

    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->accountTypeRepository->listAll($this->formatFields($select));
        } else
            $data = AccountTypeResource::collection($this->accountTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param AccountTypeRequest $request
     * @return mixed
     */
    public function store(AccountTypeRequest $request)
    {
        $save = $this->accountTypeRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! AccountType has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $accountType = $this->accountTypeRepository->getById($uuid);

        if (!$accountType) {
            return $this->respondNotFound('AccountType not found.');
        }
        return $this->respondWithData(new AccountTypeResource($accountType));

    }

    /**
     * @param AccountTypeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(AccountTypeRequest $request, $uuid)
    {
        $save = $this->accountTypeRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! AccountType has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->accountTypeRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! AccountType has been deleted');
        }
        return $this->respondNotFound('AccountType not deleted');
    }
}
