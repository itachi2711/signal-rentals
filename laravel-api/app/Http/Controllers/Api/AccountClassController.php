<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:37
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;

use App\Rental\Repositories\Contracts\AccountInterface;
use Illuminate\Http\Request;

class AccountClassController extends ApiController
{
    /**
     * @var AccountInterface
     */
    protected $accountRepository, $load;

    /**
     * AccountController constructor.
     * @param AccountInterface $accountInterface
     */
    public function __construct(AccountInterface $accountInterface)
    {
        $this->accountRepository = $accountInterface;
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
            return $this->accountRepository->listAll($this->formatFields($select));
        } else
            $data = AccountResource::collection($this->accountRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param AccountRequest $request
     * @return mixed
     */
    public function store(AccountRequest $request)
    {
        $save = $this->accountRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Account has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $account = $this->accountRepository->getById($uuid);

        if (!$account) {
            return $this->respondNotFound('Account not found.');
        }
        return $this->respondWithData(new AccountResource($account));

    }

    /**
     * @param AccountRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(AccountRequest $request, $uuid)
    {
        $save = $this->accountRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! Account has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->accountRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Account has been deleted');
        }
        return $this->respondNotFound('Account not deleted');
    }
}
