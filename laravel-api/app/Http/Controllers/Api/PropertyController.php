<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PropertyRequest;
use App\Http\Resources\PeriodResource;
use App\Http\Resources\PropertyResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\PropertyInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PropertyController extends ApiController
{
    /**
     * @var PropertyInterface
     */
    protected $propertyRepository, $load, $accountRepository, $unitRepository, $landlordRepository, $invoiceRepository;

    /**
     * PropertyController constructor.
     * @param PropertyInterface $propertyInterface
     * @param UnitInterface $unitRepository
     * @param LandlordInterface $landlordRepository
     * @param InvoiceInterface $invoiceRepository
     */
    public function __construct(PropertyInterface $propertyInterface, UnitInterface $unitRepository,
                                LandlordInterface $landlordRepository, InvoiceInterface $invoiceRepository)
    {
        $this->propertyRepository = $propertyInterface;
        $this->landlordRepository = $landlordRepository;
        $this->unitRepository = $unitRepository;
        $this->invoiceRepository = $invoiceRepository;
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
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            $data = PropertyResource::collection($this->propertyRepository->listAll($this->formatFields($select), [
                'units',
                'extra_charges',
                'late_fees',
                'utility_costs',
                'payment_methods'
            ]));
        } else
            $data = PropertyResource::collection($this->propertyRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param PropertyRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(PropertyRequest $request)
    {
        try {
            DB::beginTransaction();
                $data = $request->all();
                $newProperty = $this->propertyRepository->create($data);

                // Payment Methods
                if(array_key_exists('paymentMethodFields', $data)){
                    $paymentMethodFields = $data['paymentMethodFields'];
                    if (isset($paymentMethodFields)){
                        foreach ($paymentMethodFields as $key => $value){
                            $newProperty->payment_methods()->attach($value['payment_method_id'],
                                [
                                    'payment_method_description'     => $value['payment_method_description']
                                ]
                            );
                        }
                    }
                }

                // Utility cost fields
                if(array_key_exists('utilityFields', $data)) {
                    $utilityCostFields = $data['utilityFields'];
                    if (isset($utilityCostFields)) {
                        foreach ($utilityCostFields as $key => $value) {
                            $newProperty->utility_costs()->attach($value['utility_id'],
                                [
                                    'utility_unit_cost'     => $value['utility_unit_cost'],
                                    'utility_base_fee'      => $value['utility_base_fee']
                                ]
                            );
                        }
                    }
                }

                // Extra Charges
                if(array_key_exists('extraChargeFields', $data)){
                    $extraChargeFields = $data['extraChargeFields'];
                    if (isset($extraChargeFields)){
                        foreach ($extraChargeFields as $key => $value){
                            $newProperty->extra_charges()->attach($value['extra_charge_id'],
                                [
                                    'extra_charge_value'        => $value['extra_charge_value'],
                                    'extra_charge_type'         => $value['extra_charge_type'],
                                    'extra_charge_frequency'    => $value['extra_charge_frequency']
                                ]
                            );
                        }
                    }
                }

                // Late Fees
                if(array_key_exists('lateFeeFields', $data)){
                    $lateFeeFields = $data['lateFeeFields'];
                    if (isset($lateFeeFields)){
                        foreach ($lateFeeFields as $key => $value){
                            $newProperty->late_fees()->attach($value['late_fee_id'],
                                [
                                    'grace_period'          => $value['grace_period'],
                                    'late_fee_value'        => $value['late_fee_value'],
                                    'late_fee_type'         => $value['late_fee_type'],
                                    'late_fee_frequency'    => $value['late_fee_frequency']
                                ]
                            );
                        }
                    }
                }

                // Property Units
                if(array_key_exists('units', $data)){
                    $units = $data['units'];
                    if (isset($units)){
                        foreach ($units as $key => $unit){
                            $unit['property_id'] = $newProperty['id'];
                            $newUnit = $this->unitRepository->create($unit);

                            // save amenities to pivot
                            if(array_key_exists('selected_amenities', $unit)){
                                $selectedAmenitiesData = $unit['selected_amenities'];
                                if (isset($selectedAmenitiesData)){
                                    foreach ($selectedAmenitiesData as $amenityKey => $amenity){
                                        $newUnit->amenities()->attach($amenity);
                                    }
                                }
                            }

                            // save utilities to pivot
                            if(array_key_exists('selected_utilities', $unit)){
                                $selectedUtilitiesData = $unit['selected_utilities'];
                                if (isset($selectedUtilitiesData)){
                                    foreach ($selectedUtilitiesData as $UtilityKey => $utility){
                                        $newUnit->utilities()->attach($utility);
                                    }
                                }
                            }
                        }
                    }
                }
                if (!isset($newProperty)) {
                    return $this->respondNotSaved('Not Saved');
                }
            DB::commit();
                $landlord = $this->landlordRepository->getById($newProperty['landlord_id']);
                CommunicationMessage::send(NEW_PROPERTY, $landlord, $newProperty);
                return $this->respondWithSuccess('Success !! Property has been created.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $property = $this->propertyRepository->getById($uuid, $this->load);
        if(!$property)
            return $this->respondNotFound('Property not found.');

        return $this->respondWithData(new PropertyResource($property));
    }

    /**
     * @param PropertyRequest $request
     * @param $id
     * @return array|mixed
     * @throws \Exception
     */
    public function update(PropertyRequest $request, $id)
    {
        if (!auth()->user()->tokenCan('edit-property'))
            throw new \Exception('Action is not allowed.');
        try {
            DB::beginTransaction();
                $data = $request->all();
                $property = $this->propertyRepository->getById($id);
                if (!$property)
                    return $this->respondNotFound('Error retrieving property');
                $this->propertyRepository->update($request->validated(), $id);

                // Payment Methods
                if(array_key_exists('paymentMethodFields', $data)){
                    $paymentMethodFields = $data['paymentMethodFields'];
                    if (isset($paymentMethodFields)){
                        $paymentMethodData = [];
                        foreach ($paymentMethodFields as $key => $value){
                            $paymentMethodData[$value['payment_method_id']] = [
                                'payment_method_description'    => $value['payment_method_description']
                            ];
                        }
                        $property->payment_methods()->sync($paymentMethodData);
                    }
                }

                // Utility cost fields
                if(array_key_exists('utilityFields', $data)) {
                    $utilityCostFields = $data['utilityFields'];
                    if (isset($utilityCostFields)) {
                        $utilityCostData = [];
                        foreach ($utilityCostFields as $key => $value) {
                            $utilityCostData[$value['utility_id']] = [
                                'utility_unit_cost'     => $value['utility_unit_cost'],
                                'utility_base_fee'      => $value['utility_base_fee']
                            ];
                        }
                        $property->utility_costs()->sync($utilityCostData);
                    }
                }

                // Extra Charges
                if(array_key_exists('extraChargeFields', $data)){
                    $extraChargeFields = $data['extraChargeFields'];
                    if (isset($extraChargeFields)){
                        $extraChargeData = [];
                        foreach ($extraChargeFields as $key => $value){
                            $extraChargeData[$value['extra_charge_id']] = [
                                    'extra_charge_value'        => $value['extra_charge_value'],
                                    'extra_charge_type'         => $value['extra_charge_type'],
                                    'extra_charge_frequency'    => $value['extra_charge_frequency']
                                ];
                        }
                        $property->extra_charges()->sync($extraChargeData);
                    }
                }

                // Late Fees
                if(array_key_exists('lateFeeFields', $data)){
                    $lateFeeFields = $data['lateFeeFields'];
                    if (isset($lateFeeFields)){
                        $lateFeeData = [];
                        foreach ($lateFeeFields as $key => $value){
                            $lateFeeData[$value['late_fee_id']] = [
                                'grace_period'          => $value['grace_period'],
                                'late_fee_value'        => $value['late_fee_value'],
                                'late_fee_type'         => $value['late_fee_type'],
                                'late_fee_frequency'    => $value['late_fee_frequency']
                            ];
                        }
                        $property->late_fees()->sync($lateFeeData);
                    }
                }
            DB::commit();
            return $this->respondWithSuccess('Success !! Property has been updated.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $uuid
     * @return array
     * @throws \Exception
     */
    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();
            if (auth()->user()->tokenCan('delete-property')) {
                $property = $this->propertyRepository->getById($uuid);
                if (!isset($property))
                    throw new \Exception('Property not found.');

                $leases = $property->leases;
                $pendingAmount = 0;
                foreach ($leases as $lease) {
                    $pendingAmount = $pendingAmount + $this->invoiceRepository->pendingLeaseAmount($lease->id);
                }
                if ($pendingAmount != 0)
                    throw new \Exception('Property has pending invoices');

                $property->notices()->delete();
                $property->invoices()->delete();
                $property->periods()->detach();
                $property->payment_methods()->detach();
                $property->late_fees()->detach();
                $property->extra_charges()->detach();
                $property->utility_costs()->detach();
                $property->leases()->each(function($lease) {
                    $lease->units()->detach();
                    $lease->tenants()->detach();
                    $lease->utility_deposits()->detach();
                    $lease->utility_charges()->detach();
                    $lease->extra_charges()->detach();
                    $lease->invoices()->delete();
                    $lease->late_fees()->detach();
                    $lease->property()->delete();
                    $lease->invoices()->delete();
                    $lease->payment_methods()->detach();
                    $lease->delete();
                });
              //  $property->leases()->delete();
                $property->units()->delete();
                $property->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Property has been deleted.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     */
    public function uploadPhoto(Request $request) {
        $data = $request->all();
        $fileNameToStore = '';
        // Upload logo
        if($request->hasFile('property_photo')) {
            $filenameWithExt = $request->file('property_photo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('property_photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('property_photo')->storeAs('photos', $fileNameToStore);
            $data['property_photo'] = $fileNameToStore;

            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'photos'.DIRECTORY_SEPARATOR. $fileNameToStore;

            // Update the property
            $this->propertyRepository->update(
                [
                    'property_photo' => $fileNameToStore
                ], $data['property_id']);
        }
        return json_encode($fileNameToStore);
        // also, delete previous image file from server
       // $this->memberRepository->update(array_filter($data), $data['id']);
    }

    /**
     * @param Request $request
     */
    public function profilePic(Request $request)
    {
        $data = $request->all();
        if( array_key_exists('file_path', $data) ) {
            $file_path = $data['file_path'];
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'photos'.DIRECTORY_SEPARATOR. $file_path;
            return response()->file($local_path);
        }
        return $this->respondNotFound('file_path not provided');
    }

    /**
     * @param Request $request
     * @return mixed
     */
	public function search(Request $request) {
        $data = $request->all();
        if (array_key_exists('filter', $data)) {
            $filter = $data['filter'];

			$data = $this->propertyRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
			return PropertyResource::collection($data);

           // return $this->propertyRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function periods(Request $request) {
        $data = $request->all();
        if (array_key_exists('id', $data)) {
            $property = $this->propertyRepository->getById($data['id']);
            return PeriodResource::collection($property->periods);
        }
        return [];
    }
}

