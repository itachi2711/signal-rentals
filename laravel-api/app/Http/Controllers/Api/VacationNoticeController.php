<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/15/2021
 * Time: 4:51 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\VacationNoticeRequest;
use App\Http\Resources\VacationNoticeResource;
use App\Rental\Repositories\Contracts\TenantInterface;
use App\Rental\Repositories\Contracts\VacationNoticeInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class VacationNoticeController extends ApiController
{
    /**
     * @var VacationNoticeInterface
     */
    protected $vacationNoticeRepository, $load, $accountRepository, $tenantRepository;

    /**
     * VacationNoticeController constructor.
     * @param VacationNoticeInterface $vacationNoticeInterface
     * @param TenantInterface $tenantRepository
     */
    public function __construct(VacationNoticeInterface $vacationNoticeInterface, TenantInterface $tenantRepository)
    {
        $this->vacationNoticeRepository = $vacationNoticeInterface;
        $this->tenantRepository = $tenantRepository;
        $this->load = ['tenant', 'lease', 'property'];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->vacationNoticeRepository->listAll($this->formatFields($select));
        } else
            $data = VacationNoticeResource::collection($this->vacationNoticeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param VacationNoticeRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(VacationNoticeRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $notice = $this->vacationNoticeRepository->create($data);

            if (!isset($notice))
                return $this->respondNotSaved('Not Saved');
            DB::commit();
            $tenant = $this->tenantRepository->getById($notice['tenant_id']);
            CommunicationMessage::send(NEW_VACATE_NOTICE, $tenant, $notice);
            return $this->respondWithSuccess('Success !! Vacation Notice has been created.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param VacationNoticeRequest $request
     * @param $id
     * @return array|mixed
     * @throws \Exception
     */
    public function update(VacationNoticeRequest $request, $id)
    {
        if (!auth()->user()->tokenCan('edit-notice'))
            throw new \Exception('Action is not allowed.');

        $save = $this->vacationNoticeRepository->update($request->all(), $id);
        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! VacationNotice has been updated.');
    }

    /**
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if (auth()->user()->tokenCan('delete-notice')) {
                $notice = $this->vacationNoticeRepository->getById($id);
                if (!isset($notice))
                    throw new \Exception('Vacation Notice not found.');
                $notice->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Vacation Notice has been deleted.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}


