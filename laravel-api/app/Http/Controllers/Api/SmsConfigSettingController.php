<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:10 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\SmsConfigSettingRequest;
use App\Http\Resources\SmsConfigSettingResource;
use App\Rental\Repositories\Contracts\SmsConfigSettingInterface;
use Illuminate\Http\Response;

class SmsConfigSettingController extends ApiController
{
    /**
     * @var SmsConfigSettingInterface
     */
    protected $smsConfigSettingRepository, $load, $accountRepository;

    /**
     * SmsConfigSettingController constructor.
     * @param SmsConfigSettingInterface $smsConfigSettingInterface
     */
    public function __construct(SmsConfigSettingInterface $smsConfigSettingInterface)
    {
        $this->smsConfigSettingRepository = $smsConfigSettingInterface;
        $this->load = ['permissions'];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->smsConfigSettingRepository->listAll($this->formatFields($select));
        } else
            $data = SmsConfigSettingResource::collection($this->smsConfigSettingRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param SmsConfigSettingRequest $request
     * @return array
     */
    public function store(SmsConfigSettingRequest $request)
    {
        $data = $request->json()->all();

        $smsConfigSetting = $this->smsConfigSettingRepository->create($data);

        if ($smsConfigSetting && array_key_exists('permission', $data)) {
            $permissions = $data['permission'];
            if (!is_null($permissions)) {
                $smsConfigSetting->permissions()->attach($permissions);
            }
            return $this->respondWithSuccess('Success !! SmsConfigSetting has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param SmsConfigSettingRequest $request
     * @param $uuid
     * @return array|mixed
     */
    public function update(SmsConfigSettingRequest $request, $uuid)
    {
        $data = $request->json()->all();
        if (array_key_exists('permissions', $data)) {
            $permissions = $data['permissions'];

            if (!is_null($permissions)) {
                $this->smsConfigSettingRepository->getById($uuid)->permissions()->sync($permissions);
            }
        }
        $this->smsConfigSettingRepository->update($request->all(), $uuid);
        return $this->respondWithSuccess('Success !! SmsConfigSetting has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->smsConfigSettingRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! SmsConfigSetting has been updated.');
    }
}
