<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 8/15/2021
 * Time: 11:37 AM
 */

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Rental\Repositories\Contracts\AccountInterface;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckLeasePrePayment
{
    /**
     * @var
     */
    protected $paymentRepository, $accountRepository, $leaseRepository;

    /**
     * CheckLeasePrePayment constructor.
     * @param PaymentInterface $paymentInterface
     * @param AccountInterface $accountRepository
     * @param LeaseInterface $leaseRepository
     */
    public function __construct(PaymentInterface $paymentInterface, AccountInterface $accountRepository,
                                LeaseInterface $leaseRepository)
    {
        $this->paymentRepository = $paymentInterface;
        $this->accountRepository = $accountRepository;
        $this->leaseRepository = $leaseRepository;
    }

    /**
     * @param InvoiceCreated $event
     */
    public function handle(InvoiceCreated $event)
    {
        $invoice = $event->invoice;
        $lease = $this->leaseRepository->getById($invoice['lease_id']);
        $leaseSuspenseAccountNumber = LEASE_SUSPENSE_CODE.'-'.$lease['lease_number'];
        $accountID = $this->accountRepository->accountIDByAccountNumber($leaseSuspenseAccountNumber);
        $accountBalance = $this->accountRepository->accountBalanceByAccountNumber($leaseSuspenseAccountNumber);
        if (isset($accountBalance) && $accountBalance < 0) {
            $balance = $accountBalance * -1;
            $excessPayment =  DB::table('journals')
                ->select(DB::raw('journals.reference_id'))
                ->where('journals.credit_account_id', $accountID)
                ->where('journals.amount', $balance)
                ->latest()
                ->first();

            if (isset($excessPayment)) {
                $payment = $this->paymentRepository->getById($excessPayment->reference_id);
                $payment['amount'] = $balance;
                $this->paymentRepository->processPayment($payment, false);
            }
        }
    }
}
