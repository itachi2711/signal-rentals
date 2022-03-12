<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/29/2020
 * Time: 11:51 AM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\JournalInterface;
use App\Rental\Repositories\Contracts\LedgerInterface;
use App\Models\Account;
use App\Models\Journal;

class JournalRepository extends BaseRepository implements JournalInterface
{
    protected $model, $ledgerRepository;

    /**
     * JournalRepository constructor.
     * @param Journal $model
     * @param LedgerInterface $ledgerRepository
     */
    function __construct(Journal $model, LedgerInterface $ledgerRepository)
    {
        $this->model = $model;
        $this->ledgerRepository = $ledgerRepository;
    }

    /**
     * @param array $data
     * @return null
     */
    public function create(array $data)
    {
        try{
            $journalEntry = $this->model->create($data);
            $this->ledgerRepository->ledgerEntry($journalEntry['id']);
        }catch (\Exception $exception){
            report($exception);
        }
        return null;
    }

    /**
     * Active user's agent
     * @return mixed
     */
    private function agentId() {
        return auth()->check() ? auth('api')->user()->agent_id : null;
    }

    /**
     * Get account from active or provided agent
     * @param $accountName
     * @param $agentId
     * @return mixed
     * @throws \Exception
     */
    private function accountId($accountName, $agentId = null) {
        if(is_null($agentId) ) {
            $agentId = $this->agentId();
        }

        $account = Account::where('agent_id', $agentId)
            ->where('account_name', $accountName)
            ->select('id')->first();
        if (!is_null($account))
            return $account['id'];
        throw new \Exception('Null Account ID');
    }

    /**
     * @param $accountName
     * @param $propertyID
     * @return mixed
     * @throws \Exception
     */
    private function getPropertyAccountID($accountName, $propertyID) {
        $account = Account::where('property_id', $propertyID)
            ->where('account_name', $accountName)
            ->select('id')->first();
        if (!is_null($account))
            return $account['id'];
        throw new \Exception('Null PropertyAccountID');
    }

    /**
     * @param $leaseNumber
     * @param $propertyID
     * @return mixed
     * @throws \Exception
     */
    private function getLeaseAccountID($leaseNumber, $propertyID) {
        $accountNumber = LEASE_ACCOUNT_CODE.'-'.$leaseNumber;
        $account = Account::where('property_id', $propertyID)
            ->where('account_number', $accountNumber)
            ->select('id')->first();
        if (!is_null($account))
            return $account['id'];
        throw new \Exception('Null Account getLeaseAccountID');
    }

    /**
     * @param $leaseNumber
     * @param $propertyID
     * @return mixed
     * @throws \Exception
     */
    private function getLeaseSuspenseAccountID($leaseNumber, $propertyID) {
        $accountNumber = LEASE_SUSPENSE_CODE.'-'.$leaseNumber;
        $account = Account::where('property_id', $propertyID)
            ->where('account_number', $accountNumber)
            ->select('id')->first();
        if (!is_null($account))
            return $account['id'];
        throw new \Exception('Null Account getLeaseSuspenseAccountID');
    }

    /**
     * @param $leaseNumber
     * @param $propertyID
     * @return mixed
     * @throws \Exception
     */
    private function getLeasePrePaymentAccountID($leaseNumber, $propertyID) {
        $accountNumber = PREPAYMENT_CODE.'-'.$leaseNumber;
        $account = Account::where('property_id', $propertyID)
            ->where('account_number', $accountNumber)
            ->select('id')->first();
        if (!is_null($account))
            return $account['id'];
        throw new \Exception('Null Account getLeasePrePaymentAccountID');
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function receivePayment($data = []) {
        $this->create(array_merge($data, [
            'debit_account_id'  => $this->getPropertyAccountID(BANK_ACCOUNT, $data['property_id']),
            'credit_account_id' => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
        ]));
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function receiveWaiver($data = []) {
        $this->create(array_merge($data, [
            'debit_account_id'  => $this->getPropertyAccountID(WAIVER_ACCOUNT, $data['property_id']),
            'credit_account_id' => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
        ]));
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function earnPrepayment($data = []) {
       /* $this->create(array_merge($data, [
            'debit_account_id'      => $this->getLeaseSuspenseAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id'     => $this->getLeasePrePaymentAccountID($data['lease_number'], $data['property_id']),
        ]));*/

        $this->create(array_merge($data, [
            'debit_account_id'  => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id' => $this->getLeaseSuspenseAccountID($data['lease_number'], $data['property_id']),
        ]));

        /// suspense account and lease account
        /// dr lease account
        /// cr suspense account
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function fromPrepayment($data = []) {
        /*$this->create(array_merge($data, [
            'debit_account_id'     => $this->getLeasePrePaymentAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id'    => $this->getLeaseSuspenseAccountID($data['lease_number'], $data['property_id']),
        ]));*/

        $this->create(array_merge($data, [
            'credit_account_id'  => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
            'debit_account_id'    => $this->getLeaseSuspenseAccountID($data['lease_number'], $data['property_id']),
        ]));

        /// Prepaid Amount - in lease account - cr
        /// dr - suspense a/c
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function earnRentDeposit($data = []) {
        $this->create(array_merge($data, [
            'debit_account_id'      => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id'     => $this->getPropertyAccountID(RENT_DEPOSIT_ACCOUNT, $data['property_id']),
        ]));
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function earnRent($data = []) {
        $this->create(array_merge($data, [
            'debit_account_id'  => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id' => $this->getPropertyAccountID(RENT_INCOME_ACCOUNT, $data['property_id']),
        ]));
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function earnUtilityDeposit($data = []) {
        $this->create(array_merge($data, [
            'debit_account_id'      => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id'     => $this->getPropertyAccountID(UTILITY_DEPOSIT_ACCOUNT, $data['property_id']),
        ]));
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function earnUtility($data = []) {
        $this->create(array_merge($data, [
            'debit_account_id'  => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id' => $this->getPropertyAccountID(UTILITY_INCOME_ACCOUNT, $data['property_id']),
        ]));
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function earnExtraCharge($data = []) {
        $this->create(array_merge($data, [
            'debit_account_id'  => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id' => $this->getPropertyAccountID(EXTRA_CHARGE_ACCOUNT, $data['property_id']),
        ]));
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function earnPenalty($data = []) {
        $this->create(array_merge($data, [
            'debit_account_id'  => $this->getLeaseAccountID($data['lease_number'], $data['property_id']),
            'credit_account_id' => $this->getPropertyAccountID(PENALTY_INCOME_ACCOUNT, $data['property_id']),
        ]));
    }

    /**
     * @param array $data
     */
    public function earnServiceFee($data = []) {
    }
}
