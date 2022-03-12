<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/21/2021
 * Time: 8:56 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Resources\PaymentResource;
use App\Models\Landlord;
use App\Models\Payment;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;

class LandlordPaymentsController extends ApiController
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
            $payments = $landlord->payments()->with([])->paginate($limit);
            if (isset($payments))
                return $this->respondWithData(PaymentResource::collection($payments));

            return $this->respondNotFound('Payments not found.');
        }
        return $this->respondNotFound('Payments not found.');
    }

    /**
     * @param Landlord $landlord
     * @param Payment $payment
     * @return mixed
     */
    public function show(Landlord $landlord, Payment $payment)
    {
        if ($this->loginProxy->checkLandlord($landlord)) {
            $data = $landlord->payments()->where('payments.id', $payment->id)->first();

            if (isset($data))
                return $this->respondWithData(PaymentResource::make($data));

            return $this->respondNotFound('Payment not found.');
        }
        return $this->respondNotFound('Payment not found.');
    }

}

