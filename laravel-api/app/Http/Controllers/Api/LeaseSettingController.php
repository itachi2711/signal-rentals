<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:16 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LeaseSettingRequest;
use App\Rental\Repositories\Contracts\LeaseSettingInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LeaseSettingController extends ApiController
{
    /**
     * @var LeaseSettingInterface
     */
    protected $leaseSettingRepository, $load, $accountRepository;

    /**
     * LeaseSettingController constructor.
     * @param LeaseSettingInterface $leaseSettingInterface
     */
    public function __construct(LeaseSettingInterface $leaseSettingInterface)
    {
        $this->leaseSettingRepository = $leaseSettingInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return $this->leaseSettingRepository->getFirst();
    }

    /**
     * @param LeaseSettingRequest $request
     * @return array|mixed
     */
    public function store(LeaseSettingRequest $request)
    {
        return $this->respondNotSaved('Not allowed');
    }

    /**
     * @param LeaseSettingRequest $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function update(LeaseSettingRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->leaseSettingRepository->update($request->validated(), $id);
            DB::commit();
            return $this->respondWithSuccess('Success !! Lease Setting has been updated.');
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
        return $this->respondNotSaved('Not allowed');
    }
}




