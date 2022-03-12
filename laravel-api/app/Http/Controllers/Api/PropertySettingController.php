<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:09 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PropertySettingRequest;
use App\Http\Resources\PropertySettingResource;
use App\Rental\Repositories\Contracts\PropertySettingInterface;
use Illuminate\Http\Response;

class PropertySettingController extends ApiController
{
    /**
     * @var PropertySettingInterface
     */
    protected $unitRepository, $load, $accountRepository;

    /**
     * PropertySettingController constructor.
     * @param PropertySettingInterface $propertySetting
     */
    public function __construct(PropertySettingInterface $propertySetting)
    {
        $this->unitRepository = $propertySetting;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->unitRepository->listAll($this->formatFields($select));
        } else
            $data = PropertySettingResource::collection($this->unitRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param PropertySettingRequest $request
     * @return array|mixed
     */
    public function store(PropertySettingRequest $request)
    {
        $data = $request->all();


        $save = $this->unitRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            // New member email / sms
            if (!is_null($save)) {
                // CommunicationMessage::send('new_member_welcome', $save, $save);
            }
            return $this->respondWithSuccess('Success !! PropertySetting has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param PropertySettingRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(PropertySettingRequest $request, $id)
    {
        $save = $this->unitRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! PropertySetting has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->unitRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! PropertySetting has been updated.');
    }
}


