<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 23/05/2020
 * Time: 19:59
 */

namespace App\Rental\Repositories\Eloquent;

use App\Events\InvoiceCreated;
use App\Http\Resources\InvoiceResource;
use App\Rental\Repositories\Contracts\AccountInterface;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\InvoiceItemInterface;
use App\Rental\Repositories\Contracts\JournalInterface;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Models\Lease;
use App\Rental\Repositories\Contracts\PeriodInterface;
use App\Rental\Repositories\Contracts\ReadingInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LeaseRepository extends BaseRepository implements LeaseInterface
{
    protected $model;

    /**
     * @var JournalInterface
     */
    private $journalRepository, $periodRepository,
        $readingRepository, $invoiceItemRepository,
        $invoiceRepository, $unitRepository, $transactionRepository, $accountRepository;

    /**
     * LeaseRepository constructor.
     * @param Lease $model
     * @param AccountInterface $accountRepository
     * @param PeriodInterface $periodRepository
     * @param UnitInterface $unitRepository
     * @param JournalInterface $journalInterface
     * @param ReadingInterface $readingInterface
     * @param InvoiceItemInterface $invoiceItemRepository
     * @param TransactionInterface $transactionRepository
     * @param InvoiceInterface $invoice
     *
     */
    function __construct(Lease $model,
                         AccountInterface $accountRepository,
                         PeriodInterface $periodRepository,
                         UnitInterface $unitRepository,
                         JournalInterface $journalInterface,
                         ReadingInterface $readingInterface,
                         InvoiceItemInterface $invoiceItemRepository,
                         TransactionInterface $transactionRepository,
                         InvoiceInterface $invoice)
    {
        $this->model = $model;
        $this->periodRepository = $periodRepository;
        $this->journalRepository = $journalInterface;
        $this->readingRepository = $readingInterface;
        $this->invoiceItemRepository = $invoiceItemRepository;
        $this->invoiceRepository = $invoice;
        $this->transactionRepository = $transactionRepository;
        $this->unitRepository = $unitRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param $date
     * @param $lease
     */
    private function updateBillingDates($date, $lease)
    {
        $nextBillingDate = $this->makeNextBillingDate($date, $lease);
        $this->update([
            'billed_on'         => $date,
            'next_billing_date' => $nextBillingDate
        ], $lease['id']);
    }

    /**
     * @param $billingDate
     * @param $lease
     * @return string
     */
    private function makeNextBillingDate($billingDate, $lease)
    {
        $generateInvoiceOn = $lease['generate_invoice_on']; // day of month

        /// New lease
        if (is_null($lease['billed_on'])) {
            if(isset($lease['skip_starting_period']) && $lease['skip_starting_period'] == true) {
              //  $billingDate = Carbon::parse($billingDate)->addMonth();
                $billingDate = Carbon::parse($billingDate)->addMonthsNoOverflow();
            }
            $nextBillingDate =  Carbon::parse($billingDate)
                ->setUnitNoOverflow('day', $generateInvoiceOn, 'month')
                ->format('Y-m-d');
            if ($nextBillingDate <= $billingDate)
                $nextBillingDate = Carbon::parse($nextBillingDate)->addMonthsNoOverflow();

            return $nextBillingDate;
        }
        return  Carbon::parse($billingDate)
            ->addMonthsNoOverflow()
            ->setUnitNoOverflow('day', $generateInvoiceOn, 'month')
            ->format('Y-m-d');
    }

    /**
     * @param $leaseId
     * @return float
     */
    private function getRentDueAmount($leaseId) {
        $invoices = InvoiceResource::collection(
            $this->invoiceRepository
                ->getModel()
                ->where('lease_id', $leaseId)
                ->where('paid_on', null)
                ->get());

        $amountOverDue = 0.00;
        foreach ($invoices as $invoice) {
            $invoiceData = $invoice->resolve();
            $amountOverDue += $invoiceData['summary']['amount_due_number'];
        }
        return $amountOverDue;
    }

    /**
     * Ren overdue is amount due for unpaid invoices whose due date is in the past
     * @param $leaseId
     * @return float
     */
    private function getRentOverDueAmount($leaseId) {
        $invoices = InvoiceResource::collection(
            $this->invoiceRepository
                ->getModel()
                ->where('lease_id', $leaseId)
                ->where('paid_on', null)
                ->where('due_date', '<', date('Y-m-d'))
                ->get());

        $amountOverDue = 0.00;
        foreach ($invoices as $invoice) {
            $invoiceRentAmount = $this->invoiceItemRepository->rentAmount($invoice['id']);
            $amountPaid = $this->transactionRepository->paidRent($invoice['id']);
             $amountOverDue += $invoiceRentAmount - $amountPaid;
        }
        return $amountOverDue;
    }

    /**
     * @param $leaseId
     * @return mixed
     * @throws \Exception
     */
    public function getRentAmount($leaseId) {
        $lease = Lease::where('id', $leaseId)
            ->select('rent_amount')->first();

        if (isset($lease))
            return $lease['rent_amount'];
        throw new \Exception('Null lease ID');
    }

    /**
     * @param $leaseId
     * @param $value
     * @param $type
     * @return float|int|null
     * @throws \Exception
     */
    public function calculateChargeAmount($leaseId, $value, $type) {
        switch ($type) {
            case 'fixed': {
                return $value;
                break;
            }
            case 'total_rent_percentage': {
                return ($value / 100) * $this->getRentAmount($leaseId);
                break;
            }
            case 'total_rent_over_due_percentage': {
                return ($value / 100) * $this->getRentOverDueAmount($leaseId);
                break;
            }
            default: {
                return null;
            }
        }
    }

     /**
     * @param $date
     * @param $lease
     * @param $invoiceID
     * @param bool $periodName
     */
    private function processRent($date, $lease, $invoiceID, $periodName)
    {
        $rentAmount = $lease['rent_amount'];
        $rentNarration = 'Rent - '.$periodName;

        // Journal entry - rent
        if ($rentAmount > 0)
            $this->journalRepository->earnRent([
                'narration'     => $rentNarration,
                'property_id'   => $lease['property_id'],
                'amount'        => $rentAmount,
                'reference_id'  => $lease['id'],
                'lease_number'  => $lease['lease_number'],
                'created_by'    => $lease['created_by']
            ]);

        // Billing for rent
        if ($rentAmount > 0)
            $this->invoiceItemRepository->item($date, $lease, [
                'invoice_id'        => $invoiceID,
                'item_name'         => $rentNarration,
                'item_type'         => ITEM_RENT,
                'item_description'  => $rentNarration,
                'quantity'          => 1,
                'price'             => $rentAmount,
                'amount'            => $rentAmount,
                'discount'          => 0,
                'tax'               => 0,
                'tax_id'            => '',
            ]);
    }

    /**
     * @param $date
     * @param $lease
     * @param $invoiceID
     * @param bool $periodName
     */
    private function processRentDeposit($date, $lease, $invoiceID,  $periodName)
    {
        $rentDepositAmount = $lease['rent_deposit'];
        $rentDepositNarration = 'Rent Deposit';

        // Journal entry for rent deposit
        if($rentDepositAmount > 0)
            $this->journalRepository->earnRentDeposit([
                'narration'     => $rentDepositNarration,
                'property_id'   => $lease['property_id'],
                'amount'        => $rentDepositAmount,
                'reference_id'  => $lease['id'],
                'lease_number'  => $lease['lease_number'],
                'created_by'    => $lease['created_by']
            ]);

        // Billing for rent deposit
        if ($rentDepositAmount > 0)
            $this->invoiceItemRepository->item($date, $lease, [
                'invoice_id'        => $invoiceID,
                'item_name'         => $rentDepositNarration,
                'item_type'         => ITEM_RENT_DEPOSIT,
                'item_description'  => $rentDepositNarration,
                'quantity'          => 1,
                'price'             => $rentDepositAmount,
                'amount'            => $rentDepositAmount,
                'discount'          => 0,
                'tax'               => 0,
                'tax_id'            => '',
            ]);
    }

    /**
     * @param $date
     * @param $lease
     * @param $invoiceID
     * @param bool $periodName
     */
    private function processUtilityDeposit($date, $lease, $invoiceID, $periodName)
    {
        if(isset($lease['utility_deposits'])) {
            $utilityDepositsData = $lease['utility_deposits'];
            if (isset($utilityDepositsData)) {
                foreach ($utilityDepositsData as $key => $value) {

                    $utilityDepositAmount = $value['pivot']['deposit_amount'];
                    $utilityDepositNarration = $value['utility_display_name']. ' - Deposit';

                    // Journal entry for utility deposit
                    if ($utilityDepositAmount > 0)
                        $this->journalRepository->earnUtilityDeposit([
                            'narration'     => $utilityDepositNarration,
                            'property_id'   => $lease['property_id'],
                            'amount'        => $utilityDepositAmount,
                            'reference_id'  => $value['id'],
                            'lease_number'  => $lease['lease_number'],
                            'created_by'    => $lease['created_by']
                        ]);

                    // Billing for utility deposit
                    if ($utilityDepositAmount > 0)
                        $this->invoiceItemRepository->item($date, $lease, [
                            'invoice_id'        => $invoiceID,
                            'item_name'         => $utilityDepositNarration,
                            'item_type'         => ITEM_UTILITY_DEPOSIT,
                            'item_description'  => $utilityDepositNarration,
                            'quantity'          => 1,
                            'price'             => $utilityDepositAmount,
                            'amount'            => $utilityDepositAmount,
                            'discount'          => 0,
                            'tax'               => 0,
                            'tax_id'            => '',
                        ]);

                }
            }
        }
    }

    /**
     * @param $date
     * @param $lease
     * @param $invoiceID
     * @param bool $periodName
     */
    private function processExtraCharge($date, $lease, $invoiceID, $periodName)
    {

        if(isset($lease['extra_charges'])) {
            $extraChargesData = $lease['extra_charges'];
            if (isset($extraChargesData)) {
                foreach ($extraChargesData as $key => $value) {

                    $extraChargeAmount = 0;
                    $extraChargeDisplayName = $value['extra_charge_display_name'];

                    $extraChargeNarration = $extraChargeDisplayName.' - '.$periodName;

                    try {
                        $extraChargeAmount = $this->calculateChargeAmount(
                            $lease['id'], $value['pivot']['extra_charge_value'], $value['pivot']['extra_charge_type']
                        );
                    } catch (\Exception $e) {
                        Log::info(json_encode('LeaseRepository - processExtraCharge - '.$e->getMessage()));
                    }

                    if (isset($extraChargeAmount) && $extraChargeAmount > 0) {
                        $this->journalRepository->earnExtraCharge([
                            'narration'             => $extraChargeNarration,
                            'lease_id'              => $lease['id'],
                            'lease_number'          => $lease['lease_number'],
                            'property_id'           => $lease['property_id'],
                            'created_by'            => $lease['created_by'],
                            'reference_id'          => $value['id'],
                            'amount'                => $extraChargeAmount,
                        ]);

                        // Billing for extra charge
                        $this->invoiceItemRepository->item($date, $lease, [
                            'invoice_id'        => $invoiceID,
                            'item_name'         => $extraChargeNarration,
                            'item_type'         => ITEM_EXTRA_CHARGE,
                            'item_description'  => $extraChargeNarration,
                            'quantity'          => 1,
                            'price'             => $extraChargeAmount,
                            'amount'            => $extraChargeAmount,
                            'discount'          => 0,
                            'tax'               => 0,
                            'tax_id'            => '',
                        ]);
                    }
                }
            }
        }
    }

    /**
     * @param $date
     * @param $lease
     * @param $invoiceID
     * @param bool $periodName
     */
    private function processUtilityCharge($date, $lease, $invoiceID, $periodName)
    {
        if(isset($lease['utility_charges'])) {
            $utilityChargesData = $lease['utility_charges'];
            if (isset($utilityChargesData)) {
                foreach ($utilityChargesData as $key => $value) {
                    $utilityID          = $value['id'];
                    $unitCost           = $value['pivot']['unit_cost'];
                    $baseFee            = $value['pivot']['base_fee'];
                    $utilityDisplayName = $value['utility_display_name'];
                    $utilityNarration   = $utilityDisplayName.' - '.$periodName;

                    // go to readings table with previous_billing_date
                    // - get the last reading that is less or equal to previous_reading_date
                    // - get the difference between this figure and the most recent reading that equal or less that today ()

                    try {
                        $consumption = $this->readingRepository->periodicalUtilityConsumption(
                            $utilityID,
                            $date,
                            $lease['billed_on']
                        );
                        $totalCost = ($consumption * $unitCost) + $baseFee;

                        if (isset($totalCost) && $totalCost > 0) {
                            // Accounting for UtilityCharge
                            $this->journalRepository->earnUtility([
                                'narration'             => $utilityNarration,
                                'lease_id'              => $lease['id'],
                                'lease_number'          => $lease['lease_number'],
                                'property_id'           => $lease['property_id'],
                                'created_by'            => $lease['created_by'],
                                'reference_id'          => $value['id'],
                                'amount'                => $totalCost,
                            ]);

                            // Billing for UtilityCharge
                            $this->invoiceItemRepository->item($date, $lease, [
                                'invoice_id'        => $invoiceID,
                                'item_name'         => $utilityNarration,
                                'item_type'         => ITEM_UTILITY,
                                'item_description'  => $utilityNarration,
                                'quantity'          => 1,
                                'price'             => $totalCost,
                                'amount'            => $totalCost,
                                'discount'          => 0,
                                'tax'               => 0,
                                'tax_id'            => '',
                            ]);
                        }
                    }catch (\Exception $exception){
                        Log::info('LeaseRepository - processUtilityCharge - '.$exception->getMessage());
                    }
                }
            }
        }
    }

    /**
     * @param $chargeAmount
     * @param $lateFeeNarration
     * @param $date
     * @param $lease
     */
    private function processLateFee($chargeAmount, $lateFeeNarration, $date, $lease)
    {
        $invoice = $this->invoiceRepository->newInvoice($date, $lease, true);

        // Journal entry for utility deposit
        if ($chargeAmount > 0)
            $this->journalRepository->earnPenalty([
                'narration'     => $lateFeeNarration,
                'property_id'   => $lease['property_id'],
                'amount'        => $chargeAmount,
                'reference_id'  => $invoice['id'],
                'lease_number'  => $lease['lease_number'],
                'created_by'    => $lease['created_by']
            ]);

        // Billing for Late fee
        if ($chargeAmount > 0)
            $this->invoiceItemRepository->item($date, $lease, [
                'invoice_id'        => $invoice['id'],
                'item_name'         => $lateFeeNarration,
                'item_type'         => ITEM_PENALTY,
                'item_description'  => $lateFeeNarration,
                'quantity'          => 1,
                'price'             => $chargeAmount,
                'amount'            => $chargeAmount,
                'discount'          => 0,
                'tax'               => 0,
                'tax_id'            => '',
            ]);
    }

    /**
     * @param $lateFeeChargedOn
     * @param $lateFeeFrequency
     * @return Carbon|false
     */
    private function nextLateFeeChargeDate($lateFeeChargedOn, $lateFeeFrequency)
    {
        switch ($lateFeeFrequency){
            case 'monthly': {
                return Carbon::create($lateFeeChargedOn)->addMonth();
                break;
            }
            case 'weekly': {
                return Carbon::create($lateFeeChargedOn)->addWeek();
                break;
            }
            case 'bi-weekly': {
                return Carbon::create($lateFeeChargedOn)->addWeeks(2);
                break;
            }
            case 'daily': {
                return Carbon::create($lateFeeChargedOn)->addDay();
                break;
            }
            case 'one_time': {
                return $lateFeeChargedOn;
                break;
            }
            default: {
                return $lateFeeChargedOn;
            }
        }
    }

    /**
     * @param array $load
     * @param string $landlordID
     * @param string $propertyID
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchActive($load = array(), $landlordID = '', $propertyID = '')
    {
        if ($landlordID != '') {
            if ($propertyID != '')
                return $this->model->with($load)
                    ->where('property_id', $propertyID)
                    ->where('landlord_id', $landlordID)
                    ->where('terminated_on', null)
                    ->get();

            return $this->model->with($load)
                ->where('terminated_on', null)
                ->where('landlord_id', $landlordID)
                ->get();
        }

        if ($propertyID != '')
            return $this->model->with($load)
                ->where('property_id', $propertyID)
                ->where('terminated_on', null)
                ->get();

        return $this->model->with($load)->where('terminated_on', null)->get();
    }

    /**
     * Must be an active lease, that's due on this given date (today?) (next_billing_date = date)
     * When billed_on field is null - this is a new lease - has never been charged
     * - charge rent
     * - rent deposit
     * - utility deposits
     * - extra charges
     * @param $date
     */
    public function newLeaseInvoice($date)
    {
        $leases =  $this->model
            ->where('terminated_on', null)
            ->where('next_billing_date', $date)
            ->orWhere('billed_on', NULL)
            ->with(['utility_charges', 'extra_charges', 'utility_deposits'])
            ->get();

        if (isset($leases)) {
            foreach ($leases as $lease) {
                $invoice    = $this->invoiceRepository->newInvoice($date, $lease);
                $invoiceID  = $invoice['id'];
                $periodID   = $invoice['period_id'];
                $periodName = $invoice['period_name'];

                // Virgin lease - has deposits
                if (is_null($lease['billed_on'])) {
                    $this->processRentDeposit($date, $lease, $invoiceID, $periodName);
                    $this->processUtilityDeposit($date, $lease, $invoiceID, $periodName);
                }

                // Accounting and billing - rent
                $this->processRent($date, $lease, $invoiceID, $periodName);

                // Accounting and billing - Extra charges
                $this->processExtraCharge($date, $lease, $invoiceID, $periodName);

                // Accounting and billing - utility_charges
                if (isset($lease['billed_on'])) {
                    $this->processUtilityCharge($date, $lease, $invoiceID, $periodName);
                }
                $this->updateBillingDates($date, $lease);
                event(new InvoiceCreated($invoice));
            }
        }
    }

    /**
     * All unpaid invoices, whose due date is in past.
     * Update the lease of when the late fee was charged.
     * @param $date
     * @throws \Exception
     */
    public function calculateLateFees($date)
    {
        $invoices = InvoiceResource::collection(
            $invoices = $this->invoiceRepository
                ->getModel()
                ->where('paid_on', null)
                ->where('due_date', '<', $date)
                ->get());

        foreach ($invoices as $invoice) {
            if (isset($invoice)) {
                $periodName = $invoice['period_name'];
                $lease = $this->getById($invoice['lease_id'], ['late_fees']);
                $leaseID = $lease['id'];
                $waivePenalty = $lease['waive_penalty'];

                $lateFees = $lease['late_fees'];
                foreach ($lateFees as $lateFee) {
                    $lateFeeID = $lateFee['id'];
                    $lateFeeName = $lateFee['late_fee_name'];
                    $lateFeeDisplayName = $lateFee['late_fee_display_name'];
                    $lateFeeValue = $lateFee['pivot']['late_fee_value'];
                    $lateFeeType = $lateFee['pivot']['late_fee_type'];
                    $lateFeeFrequency = $lateFee['pivot']['late_fee_frequency'];
                    $lateFeeGracePeriod = $lateFee['pivot']['grace_period'];

                    $dateAfterGracePeriod = Carbon::create($invoice['due_date'])->addDays($lateFeeGracePeriod);

                    if ($date < $dateAfterGracePeriod)
                        return;

                    $lateFeeChargedOn = $invoice['late_fee_charged_on'];
                    if ($lateFeeChargedOn != null) {
                        $dateAfterLateFeeFrequency = $this->nextLateFeeChargeDate($lateFeeChargedOn, $lateFeeFrequency);
                        if ($dateAfterLateFeeFrequency != $date)
                            return;
                    }

                    $lateFeeNarration = $lateFeeDisplayName.' - '.$periodName;
                    $chargeAmount = $this->calculateChargeAmount($leaseID, $lateFeeValue, $lateFeeType);

                    if ($chargeAmount > 0 && !$waivePenalty) {
                        $this->processLateFee($chargeAmount, $lateFeeNarration, $date, $lease);
                        $this->invoiceRepository->update([
                            'late_fee_charged_on' => $date
                        ], $invoice['id']);
                    }
                }
            }
        }
    }
}
