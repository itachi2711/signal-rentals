<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UtilityBillRequest;
use App\Http\Resources\UnitUtilityBillResource;
use App\Rental\Repositories\Contracts\UnitUtilityBillInterface;
use App\Rental\Repositories\Contracts\UtilityBillInterface;
use Illuminate\Http\Response;

class UtilityBillController extends ApiController
{
    /**
     * @var UtilityBillInterface
     */
    protected $utilityBillRepository, $unitUtilityBillRepository, $load;

    /**
     * UtilityBillController constructor.
     * @param UtilityBillInterface $utilityInterface
     * @param UnitUtilityBillInterface $unitUtilityBillRepository
     */
    public function __construct(UtilityBillInterface $utilityInterface, UnitUtilityBillInterface $unitUtilityBillRepository)
    {
        $this->utilityBillRepository = $utilityInterface;
        $this->unitUtilityBillRepository = $unitUtilityBillRepository;
       // $this->load = ['utility', 'property', 'unit_utility_bills'];
        $this->load = ['unit', 'utility_bill'];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->utilityBillRepository->listAll($this->formatFields($select), []);
        } else
           // $data = UtilityBillResource::collection($this->utilityBillRepository->getAll($this->load, true));
            $data = UnitUtilityBillResource::collection(
                $this->unitUtilityBillRepository->getAllPaginate($this->load)
            );

        return $this->respondWithData($data);
    }

    /**
     * @param UtilityBillRequest $request
     */
    public function store(UtilityBillRequest $request)
    {
        $data = $request->all();
        $newUtilityBill = $this->utilityBillRepository->create($data);

        // Unit UtilityBill data
        if(array_key_exists('unitBills', $data)) {
            $unitBillsData = $data['unitBills'];
            if (isset($unitBillsData)){
                foreach ($unitBillsData as $key => $value){
                    // Last two fields are redundant, needed for more efficient frontend sort
                    $newUtilityBill->unit_utility_bills()->attach($value['unit_id'],
                        [
                            'unit_id'           => $value['unit_id'],
                            'reading_date'      => date('Y-m-d H:i:s', strtotime($value['reading_date'])),
                            'current_reading'   => $value['current_reading'],
                            'property_id'       => $data['property_id'],
                            'utility_id'        => $data['utility_id'],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Update the specified resource in storage.
     * @param UtilityBillRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(UtilityBillRequest $request, $id)
    {
        $save = $this->utilityBillRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! UtilityBillBill has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->utilityBillRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! UtilityBillBill has been updated.');
    }
}



