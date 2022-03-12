<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/20/2021
 * Time: 2:10 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Requests\TenantProfileRequest;
use App\Http\Resources\TenantResource;
use App\Rental\Repositories\Contracts\TenantInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantProfileController extends ApiController
{
    /**
     * @var TenantInterface
     */
    protected $tenantRepository, $loginProxy;

    /**
     * TenantProfileController constructor.
     * @param TenantInterface $tenantInterface
     * @param LoginProxy $loginProxy
     */
    public function __construct(TenantInterface $tenantInterface, LoginProxy $loginProxy)
    {
        $this->tenantRepository = $tenantInterface;
        $this->loginProxy = $loginProxy;
    }

    /**
     * @param Request $request
     * @return mixed|void
     */
    public function index(Request $request)
    {
        $tenant = Auth::user();
        $tenant = $this->tenantRepository->getById($tenant->id);

        if (!isset($tenant) || $tenant->id != Auth::user()->id) {
            $this->loginProxy->logout();
            return;
        }

        if (!$tenant) {
            return $this->respondNotFound('Tenant not found.');
        }
        return $this->respondWithData(new TenantResource($tenant));
    }

    /**
     * @param TenantProfileRequest $request
     * @param $uuid
     * @return array|mixed|void
     */
    public function update(TenantProfileRequest $request, $uuid)
    {
        $tenant = Auth::user();
        if (!isset($tenant) || $tenant->id != $uuid) {
            $this->loginProxy->logout();
            return;
        }
        $doNotUpdate = [
            'confirmed' => 1
        ];
        $data = array_diff_key($request->all(), $doNotUpdate);
        $save = $this->tenantRepository->update(array_filter($data), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Tenant has been updated.');
        }
    }
}
