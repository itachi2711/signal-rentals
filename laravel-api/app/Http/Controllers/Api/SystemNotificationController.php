<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/10/2021
 * Time: 9:31 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\SystemNotificationRequest;
use App\Http\Resources\SystemNotificationResource;
use App\Rental\Repositories\Contracts\SystemNotificationInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SystemNotificationController extends ApiController
{
    /**
     * @var SystemNotificationInterface
     */
    protected $systemNotificationRepository, $load, $accountRepository;

    /**
     * SystemNotificationController constructor.
     * @param SystemNotificationInterface $systemNotificationInterface
     */
    public function __construct(SystemNotificationInterface $systemNotificationInterface)
    {
        $this->systemNotificationRepository = $systemNotificationInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->systemNotificationRepository->listAll($this->formatFields($select));
        } else
            $data = SystemNotificationResource::collection($this->systemNotificationRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param SystemNotificationRequest $request
     * @return array
     */
    public function store(SystemNotificationRequest $request)
    {
        $data = $request->json()->all();

        $systemNotification = $this->systemNotificationRepository->create($data);

        return $this->respondWithSuccess('Success !! SystemNotification has been created.');

    }

    /**
     * @param SystemNotificationRequest $request
     * @param $uuid
     * @return array
     * @throws \Exception
     */
    public function update(SystemNotificationRequest $request, $uuid)
    {
        try {
            DB::beginTransaction();
            $this->systemNotificationRepository->update($request->validated(), $uuid);
            DB::commit();
            return $this->respondWithSuccess('Success !! SystemNotification has been updated.');
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
        $save = $this->systemNotificationRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! SystemNotification has been updated.');
    }
}
