<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/21/2021
 * Time: 8:55 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Resources\VacationNoticeResource;
use App\Models\Landlord;
use App\Models\VacationNotice;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;

class LandlordNoticesController extends ApiController
{
    /**
     * @var PaymentInterface
     */
    protected $landlordRepository, $load, $loginProxy;

    /**
     * LandlordPropertiesController constructor.
     * @param LandlordInterface $landlordRepository
     * @param LoginProxy $loginProxy
     */
    public function __construct(LandlordInterface $landlordRepository, LoginProxy $loginProxy)
    {
        $this->landlordRepository = $landlordRepository;
        $this->loginProxy = $loginProxy;
        $this->load = [];
    }

    /**
     * @param Landlord $landlord
     * @return mixed
     */
    public function index(Landlord $landlord)
    {
        if ($this->loginProxy->checkLandlord($landlord)) {
            $limit = $this->landlordRepository->limit();
            $notices = $landlord->notices()->with([])->paginate($limit);
            if (isset($notices))
                return $this->respondWithData(VacationNoticeResource::collection($notices));

            return $this->respondNotFound('VacationNotices not found.');
        }
        return $this->respondNotFound('VacationNotice not found.');
    }

    /**
     * @param Landlord $landlord
     * @param VacationNotice $notice
     * @return mixed
     */
    public function show(Landlord $landlord, VacationNotice $notice)
    {
        if ($this->loginProxy->checkLandlord($landlord)) {
            $data = $landlord->notices()->where('notices.id', $notice->id)->first();
            if (isset($data))
                return $this->respondWithData(VacationNoticeResource::make($data));
            return $this->respondNotFound('VacationNotice not found.');
        }
        return $this->respondNotFound('VacationNotice not found.');
    }

}

