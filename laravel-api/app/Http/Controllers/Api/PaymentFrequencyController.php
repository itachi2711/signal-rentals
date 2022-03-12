<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:16 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentFrequencyRequest;
use App\Http\Resources\PaymentFrequencyResource;
use App\Rental\Repositories\Contracts\PaymentFrequencyInterface;
use Illuminate\Http\Response;

class PaymentFrequencyController extends ApiController
{
    /**
     * @var PaymentFrequencyInterface
     */
    protected $paymentFrequencyRepository, $load, $accountRepository;

    /**
     * PaymentFrequencyController constructor.
     * @param PaymentFrequencyInterface $paymentFrequencyInterface
     */
    public function __construct(PaymentFrequencyInterface $paymentFrequencyInterface)
    {
        $this->paymentFrequencyRepository = $paymentFrequencyInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->paymentFrequencyRepository->listAll($this->formatFields($select));
        } else
            $data = PaymentFrequencyResource::collection($this->paymentFrequencyRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param PaymentFrequencyRequest $request
     * @return array|mixed
     */
    public function store(PaymentFrequencyRequest $request)
    {
        $data = $request->all();


        $save = $this->paymentFrequencyRepository->create($data);


        return $save;


        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            // New member email / sms
            if (!is_null($save)) {
                // CommunicationMessage::send('new_member_welcome', $save, $save);
            }
            return $this->respondWithSuccess('Success !! PaymentFrequency has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param PaymentFrequencyRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(PaymentFrequencyRequest $request, $id)
    {
        $save = $this->paymentFrequencyRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! PaymentFrequency has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->paymentFrequencyRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! PaymentFrequency has been updated.');
    }
}


