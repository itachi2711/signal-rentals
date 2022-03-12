<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:12 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\TenantSettingRequest;
use App\Rental\Repositories\Contracts\TenantSettingInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TenantSettingController extends ApiController
{
    /**
     * @var TenantSettingInterface
     */
    protected $tenantSettingRepository, $load, $accountRepository;

    /**
     * TenantSettingController constructor.
     * @param TenantSettingInterface $tenantSetting
     */
    public function __construct(TenantSettingInterface $tenantSetting)
    {
        $this->tenantSettingRepository = $tenantSetting;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
		return $this->tenantSettingRepository->getFirst();
    }

    /**
     * @param TenantSettingRequest $request
     * @return array|mixed
     */
    public function store(TenantSettingRequest $request)
    {
        return $this->respondNotSaved('Not allowed');
    }

    /**
     * @param TenantSettingRequest $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function update(TenantSettingRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->tenantSettingRepository->update($request->validated(), $id);
            DB::commit();
            return $this->respondWithSuccess('Success !! TenantSetting has been updated.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->tenantSettingRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! TenantSetting has been updated.');
    }
}



