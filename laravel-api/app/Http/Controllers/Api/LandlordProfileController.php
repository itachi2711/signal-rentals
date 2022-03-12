<?php
/**
 * Created by PhpStorm.
 * Landlord: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/20/2021
 * Time: 2:10 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Requests\LandlordProfileRequest;
use App\Http\Resources\LandlordResource;
use App\Rental\Repositories\Contracts\LandlordInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordProfileController extends ApiController
{
    /**
     * @var LandlordInterface
     */
    protected $landlordRepository, $loginProxy;

    /**
     * LandlordProfileController constructor.
     * @param LandlordInterface $landlordInterface
     * @param LoginProxy $loginProxy
     */
    public function __construct(LandlordInterface $landlordInterface, LoginProxy $loginProxy)
    {
        $this->landlordRepository = $landlordInterface;
        $this->loginProxy = $loginProxy;
    }

    /**
     * @param Request $request
     * @return mixed|void
     */
    public function index(Request $request)
    {
        if (!Auth::check())
            return $this->respondNotFound('Landlord not found.');

        $userID = Auth::user()->id;
        $landlord = $this->landlordRepository->getById($userID);

        if (!isset($landlord) || $landlord->id != $userID) {
            $this->loginProxy->logout();
            return;
        }

        if (!$landlord) {
            return $this->respondNotFound('Landlord not found.');
        }
        return $this->respondWithData(new LandlordResource($landlord));
    }

    /**
     * @param LandlordProfileRequest $request
     * @param $uuid
     * @return array|mixed|void
     */
    public function update(LandlordProfileRequest $request, $uuid)
    {
        $landlord = Auth::user();

        if (!isset($landlord) || $landlord->id != $uuid) {
            $this->loginProxy->logout();
            return;
        }
        $doNotUpdate = [
            'confirmed' => 1
        ];
        $data = array_diff_key($request->all(), $doNotUpdate);
        $save = $this->landlordRepository->update(array_filter($data), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Landlord has been updated.');
        }
    }
}
