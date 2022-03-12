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
use App\Http\Resources\LeaseResource;
use App\Models\Lease;
use App\Models\Tenant;
use App\Rental\Repositories\Contracts\TenantInterface;

class TenantLeasesController extends ApiController
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
        $this->load = [
            'property',
            'lease_type',
            'lease_mode',
            'utility_deposits',
            'utility_charges',
            'extra_charges',
            'late_fees',
            'tenants',
            'units',
            'payment_methods',
            'terminate_user'
        ];
    }

    /**
     * @param Tenant $tenant
     * @return mixed
     */
    public function index(Tenant $tenant)
    {
        if ($this->loginProxy->checkTenant($tenant)) {
            $limit = $this->tenantRepository->limit();
            $leases = $tenant->leases()->with($this->load)->paginate($limit);

            if (isset($leases))
                return $this->respondWithData(LeaseResource::collection($leases));

            return $this->respondNotFound('Lease not found.');
        }
        return $this->respondNotFound('Lease not found.');
    }

    /**
     * @param Tenant $tenant
     * @param Lease $lease
     * @return mixed
     */
    public function show(Tenant $tenant, Lease $lease)
    {
        if ($this->loginProxy->checkTenant($tenant)) {
            $data = $tenant->leases()->where('leases.id', $lease->id)->first();

            if (isset($data))
                return $this->respondWithData(LeaseResource::make($data));

            return $this->respondNotFound('Lease not found.');
        }
        return $this->respondNotFound('Lease not found.');
    }

}



