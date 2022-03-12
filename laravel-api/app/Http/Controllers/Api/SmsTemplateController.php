<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:28 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\SmsTemplateRequest;
use App\Http\Resources\SmsTemplateResource;
use App\Rental\Repositories\Contracts\SmsTemplateInterface;
use Illuminate\Http\Request;

class SmsTemplateController extends ApiController
{
    /**
     * @var SmsTemplateInterface
     */
    protected $smsTemplateRepository;

    /**
     * SmsTemplateController constructor.
     * @param SmsTemplateInterface $smsTemplateInterface
     */
    public function __construct(SmsTemplateInterface $smsTemplateInterface)
    {
        $this->smsTemplateRepository = $smsTemplateInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->smsTemplateRepository->listAll($this->formatFields($select));
        } else
            $data = SmsTemplateResource::collection($this->smsTemplateRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param SmsTemplateRequest $request
     * @return mixed
     */
    public function store(SmsTemplateRequest $request)
    {
        $save = $this->smsTemplateRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! SmsTemplate has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $smsTemplate = $this->smsTemplateRepository->getById($uuid);

        if (!$smsTemplate) {
            return $this->respondNotFound('SmsTemplate not found.');
        }
        return $this->respondWithData(new SmsTemplateResource($smsTemplate));
    }

    /**
     * @param SmsTemplateRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(SmsTemplateRequest $request, $uuid)
    {
        $save = $this->smsTemplateRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! SmsTemplate has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->smsTemplateRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! SmsTemplate has been deleted');
        }
        return $this->respondNotFound('SmsTemplate not deleted');
    }
}
