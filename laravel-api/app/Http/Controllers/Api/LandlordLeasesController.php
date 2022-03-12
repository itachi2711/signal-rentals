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
use App\Http\Resources\LeaseResource;
use App\Models\Landlord;
use App\Models\Lease;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;

class LandlordLeasesController extends ApiController
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
     * @param Landlord $landlord
     * @return mixed
     */
    public function index(Landlord $landlord)
    {
        if ($this->loginProxy->checkLandlord($landlord)) {
            $limit = $this->landlordRepository->limit();
            $leases = $landlord->leases()->with($this->load)->paginate($limit);
            if (isset($leases))
                return $this->respondWithData(LeaseResource::collection($leases));

            return $this->respondNotFound('Leases not found.');
        }
        return $this->respondNotFound('Leases not found.');
    }

    /**
     * @param Landlord $landlord
     * @param Lease $lease
     * @return mixed
     */
    public function show(Landlord $landlord, Lease $lease)
    {
        if ($this->loginProxy->checkLandlord($landlord)) {
            $data = $landlord->leases()->where('leases.id', $lease->id)->first();

            if (isset($data))
                return $this->respondWithData(LeaseResource::make($data));

            return $this->respondNotFound('Lease not found.');
        }
        return $this->respondNotFound('Lease not found.');
    }

}

