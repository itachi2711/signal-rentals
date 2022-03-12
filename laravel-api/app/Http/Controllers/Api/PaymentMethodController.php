<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:16 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\Payment;
use App\Rental\Repositories\Contracts\PaymentInterface;
use App\Rental\Repositories\Contracts\PaymentMethodInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends ApiController
{
    /**
     * @var PaymentMethodInterface
     */
    protected $paymentMethodRepository, $load, $accountRepository, $paymentRepository;

    /**
     * PaymentMethodController constructor.
     * @param PaymentMethodInterface $paymentMethodInterface
     * @param PaymentInterface $paymentRepository
     */
    public function __construct(PaymentMethodInterface $paymentMethodInterface, PaymentInterface $paymentRepository)
    {
        $this->paymentMethodRepository = $paymentMethodInterface;
        $this->paymentRepository = $paymentRepository;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->paymentMethodRepository->listAll($this->formatFields($select));
        } else
            $data = PaymentMethodResource::collection($this->paymentMethodRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param PaymentMethodRequest $request
     * @return array|mixed
     */
    public function store(PaymentMethodRequest $request)
    {
        $data = $request->all();
        $save = $this->paymentMethodRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! PaymentMethod has been created.');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $paymentMethod = $this->paymentMethodRepository->getById($id);
        if (!$paymentMethod) {
            return $this->respondNotFound('PaymentMethod not found.');
        }
        return $this->respondWithData(new PaymentMethodResource($paymentMethod));
    }

    /**
     * Update the specified resource in storage.
     * @param PaymentMethodRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(PaymentMethodRequest $request, $id)
    {
        $save = $this->paymentMethodRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! PaymentMethod has been updated.');
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
            if (auth()->user()->tokenCan('manage-setting')) {
                $paymentMethod = $this->paymentMethodRepository->getById($id);
                if (!isset($paymentMethod))
                    throw new \Exception('Payment Method not found.');

                $payment = $this->paymentRepository->getLatestWhere('payment_method_id', $id);
                if (isset($payment))
                    throw new \Exception('Payment Method has active payments');

                $paymentMethod->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Payment Method has been deleted.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}


