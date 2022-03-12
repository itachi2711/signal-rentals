<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 10:52 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\LeaseResource;
use App\Models\Property;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\PropertyInterface;

class PropertyLeasesController extends ApiController
{
    /**
     * @var LeaseInterface
     */
    protected $propertyRepository, $load;

    /**
     * LeaseController constructor.
     * @param PropertyInterface $propertyInterface
     */
    public function __construct(PropertyInterface $propertyInterface)
    {
        $this->propertyRepository = $propertyInterface;
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
     * @param Property $property
     * @return mixed
     */
    public function index(Property $property)
    {
        $limit = $this->propertyRepository->limit();
        $leases = $property->leases()->with($this->load)->paginate($limit);
        if (isset($leases))
            return $this->respondWithData(LeaseResource::collection($leases));

        return $this->respondNotFound('Leases not found.');
    }

}
