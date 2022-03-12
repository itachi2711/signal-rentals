<?php

namespace App\Http\Controllers\Api;

use App\Events\LeaseCreated;
use App\Http\Requests\LeaseRequest;
use App\Http\Resources\LeaseResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\InvoiceItemInterface;
use App\Rental\Repositories\Contracts\JournalInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaseController extends ApiController
{
    /**
     * @var LeaseInterface
     */
    protected $leaseRepository, $load, $accountRepository,
        $unitRepository, $journalRepository,
        $invoiceRepository, $invoiceItemRepository, $landlordRepository;

    /**
     * LeaseController constructor.
     * @param LeaseInterface $leaseInterface
     * @param UnitInterface $unitRepository
     * @param JournalInterface $journalInterface
     * @param InvoiceInterface $invoiceRepository
     * @param InvoiceItemInterface $invoiceItemRepository
     * @param LandlordInterface $landlordRepository
     */
    public function __construct(LeaseInterface $leaseInterface, UnitInterface $unitRepository,
                                JournalInterface $journalInterface, InvoiceInterface $invoiceRepository,
                                InvoiceItemInterface $invoiceItemRepository, LandlordInterface $landlordRepository)
    {
        $this->leaseRepository = $leaseInterface;
        $this->journalRepository = $journalInterface;
        $this->landlordRepository = $landlordRepository;
        $this->unitRepository = $unitRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceItemRepository = $invoiceItemRepository;

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
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->leaseRepository->listAll($this->formatFields($select), []);
        } else
            $data = LeaseResource::collection($this->leaseRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param LeaseRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(LeaseRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
         /*   if(array_key_exists('skip_starting_period', $data) && $data['skip_starting_period']) {
                $leaseStartDate = $data['start_date'];
                $dateAfterSkippingFirstPeriod = Carbon::parse($leaseStartDate)->addMonthsNoOverflow();
                $nextBillingDate = Carbon::parse($leaseStartDate)
                    ->addMonthsNoOverflow()
                    ->setUnitNoOverflow('day', $data['generate_invoice_on'], 'month')
                    ->format('Y-m-d');
                $data['billed_on'] = $leaseStartDate;
                $data['next_billing_date'] = $nextBillingDate;
            }*/
            $newLease = $this->leaseRepository->create($data);
            if(array_key_exists('utilityDeposits', $data)) {
                $utilityDepositsData = $data['utilityDeposits'];
                if (isset($utilityDepositsData)) {
                    foreach ($utilityDepositsData as $key => $value) {
                        if (array_key_exists('deposit_amount', $value) && $value['deposit_amount'] > 0)
                            $newLease->utility_deposits()->attach($value['utility_id'],
                                [
                                    'utility_id'        => $value['utility_id'],
                                    'deposit_amount'    => $value['deposit_amount']
                                ]
                            );
                    }
                }
            }

            // Extra Charges
            if(array_key_exists('extraCharges', $data)) {
                $extraChargesData = $data['extraCharges'];
                if (isset($extraChargesData)) {
                    foreach ($extraChargesData as $key => $value) {
                        if (array_key_exists('extra_charge_value', $value) && $value['extra_charge_value'] > 0)
                            $newLease->extra_charges()->attach($value['extra_charge_id'],
                                [
                                    'extra_charge_id'           => $value['extra_charge_id'],
                                    'extra_charge_value'        => $value['extra_charge_value'],
                                    'extra_charge_type'         => $value['extra_charge_type'],
                                    'extra_charge_frequency'    => $value['extra_charge_frequency']
                                ]
                            );
                    }
                }
            }

            // Late Fees
            if(array_key_exists('lateFeeFields', $data)) {
                $lateFeeFields = $data['lateFeeFields'];
                if (isset($lateFeeFields)){
                    foreach ($lateFeeFields as $key => $value){
                        if (array_key_exists('late_fee_value', $value) && $value['late_fee_value'] > 0)
                            $newLease->late_fees()->attach($value['late_fee_id'],
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

            if(array_key_exists('utilityCharges', $data)) {
                $utilityChargesData = $data['utilityCharges'];
                if (isset($utilityChargesData)) {
                    foreach ($utilityChargesData as $key => $value) {
                        if ($value['utility_unit_cost'] > 0 || $value['utility_base_fee'] > 0)
                            $newLease->utility_charges()->attach($value['utility_id'],
                                [
                                    'utility_id'    => $value['utility_id'],
                                    'utility_unit_cost'     => $value['utility_unit_cost'],
                                    'utility_base_fee'      => $value['utility_base_fee']
                                ]
                            );
                    }
                }
            }

            // Payment Methods
            if(array_key_exists('paymentMethodFields', $data)){
                $paymentMethodFields = $data['paymentMethodFields'];
                if (isset($paymentMethodFields)){
                    foreach ($paymentMethodFields as $key => $value){
                        $newLease->payment_methods()->attach($value['payment_method_id'],
                            [
                                'payment_method_description'     => $value['payment_method_description']
                            ]
                        );
                    }
                }
            }

            if(array_key_exists('tenants', $data)) {
                $tenantsData = $data['tenants'];
                if (isset($tenantsData)){
                    foreach ($tenantsData as $key => $value) {
                        $newLease->tenants()->attach($value['id'],
                            [
                                'tenant_id' => $value['id']
                            ]
                        );
                    }
                }
            }

            if(array_key_exists('units', $data)) {
                $unitsData = $data['units'];
                if (isset($unitsData)){
                    foreach ($unitsData as $key => $value) {
                        $newLease->units()->attach($value['id'],
                            [
                                'unit_id'   => $value['id'],
                                'unit_name' => $value['unit_name']
                            ]
                        );
                    }
                }
            }
            if (!isset($newLease)) {
                return $this->respondNotSaved('Not Saved');
            }
            DB::commit();
            event(new LeaseCreated());
            $landlord = $this->landlordRepository->getById($newLease['landlord_id']);
            CommunicationMessage::send(NEW_LEASE, $landlord, $newLease);
            return $this->respondWithSuccess('Success !! Lease has been created.');
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
        $lease = $this->leaseRepository->getById($uuid, $this->load);
        if (!$lease) {
            return $this->respondNotFound('Lease not found.');
        }
        return $this->respondWithData(new LeaseResource($lease));
    }

    /**
     * @param LeaseRequest $request
     * @param $id
     * @return array|mixed
     * @throws \Exception
     */
    public function update(LeaseRequest $request, $id)
    {
        if (!auth()->user()->tokenCan('edit-lease'))
            throw new \Exception('Action is not allowed.');
        try {
            DB::beginTransaction();
                $data = $request->all();
                $lease = $this->leaseRepository->getById($id);
                if (!$lease)
                    return $this->respondNotFound('Error retrieving lease');
                if (isset($lease['terminated_on']))
                    throw new \Exception('Error !! Terminated Lease cannot be edited.');
                $this->leaseRepository->update($request->validated(), $id);
                // Extra Charges
                if(array_key_exists('extraCharges', $data)){
                    $extraChargeFields = $data['extraCharges'];
                    if (isset($extraChargeFields)){
                        $extraChargeData = [];
                        foreach ($extraChargeFields as $key => $value){
                            $extraChargeData[$value['extra_charge_id']] = [
                                'extra_charge_value'        => $value['extra_charge_value'],
                                'extra_charge_type'         => $value['extra_charge_type'],
                                'extra_charge_frequency'    => $value['extra_charge_frequency']
                            ];
                        }
                        $lease->extra_charges()->sync($extraChargeData);
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
                        $lease->late_fees()->sync($lateFeeData);
                    }
                }

                // Utility charges
                if(array_key_exists('utilityCharges', $data)) {
                    $utilityCostFields = $data['utilityCharges'];
                    if (isset($utilityCostFields)) {
                        $utilityCostData = [];
                        foreach ($utilityCostFields as $key => $value) {
                            $utilityCostData[$value['utility_id']] = [
                                'utility_unit_cost'     => $value['utility_unit_cost'],
                                'utility_base_fee'      => $value['utility_base_fee']
                            ];
                        }
                        $lease->utility_charges()->sync($utilityCostData);
                    }
                }

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
                        $lease->payment_methods()->sync($paymentMethodData);
                    }
                }
                DB::commit();
                return $this->respondWithSuccess('Success !! Lease has been updated.');
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
            if (auth()->user()->tokenCan('delete-lease')) {
                $pendingAmount = $this->invoiceRepository->pendingLeaseAmount($uuid);
                if ($pendingAmount != 0)
                    throw new \Exception('Lease has pending invoices');
                $lease = $this->leaseRepository->getById($uuid);
                if (!isset($lease))
                    throw new \Exception('Lease not found.');
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
                DB::commit();
                return $this->respondWithSuccess('Success !! Lease has been deleted.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return |null
     */
	public function search(Request $request) {
        $data = $request->all();
        if (array_key_exists('filter', $data)) {
            $filter = $data['filter'];
            return $this->leaseRepository->search($filter);
        }
        return null;
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function terminate(Request $request) {
        if (!auth()->user()->tokenCan('terminate-lease'))
            throw new \Exception('Action is not allowed.');
        try {
            DB::beginTransaction();
            $data = $request->all();
            $leaseID = $data['lease_id'];

            if ($this->invoiceRepository->leaseHasPendingInvoices($leaseID))
                throw new \Exception('Lease has uncleared invoices');

            if (array_key_exists('end_date', $data)) {
                $this->leaseRepository->update([
                    'terminated_on' => $data['end_date'],
                    'terminated_by' => auth()->user()->id,
                ], $leaseID);
            }
            DB::commit();
            $lease = $this->leaseRepository->getById($leaseID);
            $landlord = $this->landlordRepository->getById($lease['landlord_id']);
            CommunicationMessage::send(TERMINATE_LEASE, $landlord, $lease);
        return $this->respondWithSuccess('Lease has been terminated');
        } catch (\Exception $exception) {
            DB::rollback();
            throw new \Exception($exception->getMessage());
        }
    }
}

