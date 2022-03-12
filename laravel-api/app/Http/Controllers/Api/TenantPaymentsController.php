<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 10:53 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Tenant;
use App\Rental\Repositories\Contracts\TenantInterface;

class TenantPaymentsController extends ApiController
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
            $payments = $tenant->payments()->with([])->paginate($limit);

            if (isset($payments))
                return $this->respondWithData(PaymentResource::collection($payments));

            return $this->respondNotFound('Payment not found.');
        }
        return $this->respondNotFound('Payment not found.');
    }

    /**
     * @param Tenant $tenant
     * @param Payment $payment
     * @return mixed
     */
    public function show(Tenant $tenant, Payment $payment)
    {
        if ($this->loginProxy->checkTenant($tenant)) {
            $data = $tenant->payments()->where('payments.id', $payment->id)->first();

            if (isset($data))
                return $this->respondWithData(PaymentResource::make($data));

            return $this->respondNotFound('Payment not found.');
        }
        return $this->respondNotFound('Payment not found.');
    }

}





