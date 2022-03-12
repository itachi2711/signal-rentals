<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 10:55 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Resources\PropertyResource;
use App\Models\Landlord;
use App\Models\Property;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;

class LandlordPropertiesController extends ApiController
{
    /**
     * @var LandlordInterface
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
            'property_type',
            'landlord',
            'payment_methods',
            'extra_charges',
            'late_fees',
            'utility_costs'
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
           $properties = $landlord->properties()->with($this->load)->paginate($limit);
           if (isset($properties))
               return $this->respondWithData(PropertyResource::collection($properties));

           return $this->respondNotFound('Properties not found.');
       }
        return $this->respondNotFound('Properties not found.');
    }

    /**
     * @param Landlord $landlord
     * @param Property $property
     * @return mixed
     */
    public function show(Landlord $landlord, Property $property)
    {
        if ($this->loginProxy->checkLandlord($landlord)) {
            $data = Property::where('landlord_id', $landlord->id)
                ->where('id', $property->id)
                ->orderBy('updated_at', 'desc')
                ->first();

            if (isset($data))
                return $this->respondWithData(PropertyResource::make($data));

            return $this->respondNotFound('Property not found.');
        }
        return $this->respondNotFound('Property not found.');
    }

}

