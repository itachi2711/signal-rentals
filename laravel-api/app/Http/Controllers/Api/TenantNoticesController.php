<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 9/7/2021
 * Time: 11:48 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\VacationNoticeResource;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\VacationNotice;
use App\Rental\Repositories\Contracts\TenantInterface;

class TenantNoticesController extends ApiController
{
    /**
     * @var TenantInterface
     */
    protected $tenantRepository, $load, $loginProxy;

    /**
     * LandlordPropertiesController constructor.
     * @param TenantInterface $tenantRepository
     * @param LoginProxy $loginProxy
     */
    public function __construct(TenantInterface $tenantRepository, LoginProxy $loginProxy)
    {
        $this->tenantRepository = $tenantRepository;
        $this->loginProxy = $loginProxy;
        $this->load = [];
    }

    /**
     * @param Tenant $tenant
     * @return mixed
     */
    public function index(Tenant $tenant)
    {
        if ($this->loginProxy->checkTenant($tenant)) {
            $limit = $this->tenantRepository->limit();
            $notices = $tenant->notices()->with([])->paginate($limit);
            if (isset($notices))
                return $this->respondWithData(VacationNoticeResource::collection($notices));
            return $this->respondNotFound('Notice not found.');
        }
        return $this->respondNotFound('Notice not found.');
    }

    /**
     * @param Tenant $tenant
     * @param VacationNotice $notice
     * @return mixed
     */
    public function show(Tenant $tenant, VacationNotice $notice)
    {
        if ($this->loginProxy->checkTenant($tenant)) {
            $data = $tenant->notices()->where('notices.id', $notice->id)->first();
            if (isset($data))
                return $this->respondWithData(VacationNoticeResource::make($data));
            return $this->respondNotFound('VacationNotice not found.');
        }
        return $this->respondNotFound('VacationNotice not found.');
    }
}





