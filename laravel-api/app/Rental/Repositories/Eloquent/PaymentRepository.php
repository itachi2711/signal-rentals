<?php

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\InvoiceItemInterface;
use App\Rental\Repositories\Contracts\JournalInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;
use App\Models\Payment;
use App\Rental\Repositories\Contracts\TransactionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentRepository extends BaseRepository implements PaymentInterface {

    protected $model, $journalRepository, $transactionRepository, $invoiceItemRepository, $invoiceRepository;

    /**
     * PaymentRepository constructor.
     * @param Payment $model
     * @param JournalInterface $journalInterface
     * @param TransactionInterface $transactionInterface
     * @param InvoiceItemInterface $invoiceItemInterface
     * @param InvoiceInterface $invoiceRepository
     */
    function __construct(Payment $model, JournalInterface $journalInterface,
                         TransactionInterface $transactionInterface,
                         InvoiceItemInterface $invoiceItemInterface,
                         InvoiceInterface $invoiceRepository)
    {
        $this->model = $model;
        $this->journalRepository = $journalInterface;
        $this->transactionRepository = $transactionInterface;
        $this->invoiceItemRepository = $invoiceItemInterface;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @param $walletAmount
     * @param $itemBalanceDue
     * @return int
     */
    private function calculateTransactionAmount($walletAmount, $itemBalanceDue) {
         switch ($walletAmount) {
            case  $walletAmount >= $itemBalanceDue:
                {
                    $transactionAmount = $itemBalanceDue;
                    break;
                }
            case  $walletAmount < $itemBalanceDue:
                {
                    $transactionAmount = $walletAmount;
                    break;
                }
            default: {
                $transactionAmount = 0;
            }
         }
         return $transactionAmount;
    }

    /**
     * @param $payment
     * @param bool $newPayment
     * @throws \Exception
     */
    public function processPayment($payment, $newPayment = true) {
        try {
            DB::beginTransaction();
            if ($newPayment) {
                $this->journalRepository->receivePayment([
                    'narration'     => 'Payment Received #'.$payment['receipt_number'],
                    'property_id'   => $payment['property_id'],
                    'amount'        => $payment['amount'],
                    'reference_id'  => $payment['id'],
                    'lease_number'  => $payment['lease_number'],
                    'created_by'    => $payment['created_by']
                ]);
            }
            $this->pay($payment, $newPayment);
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            Log::info(json_encode('- ERROR - payBills - '));
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $payment
     * @param $newPayment
     * @throws \Exception
     */
    public function pay($payment, $newPayment)
    {
        try {
            DB::beginTransaction();
            $today = date('Y-m-d');
            $wallet = $payment['amount'];
            $leaseID = $payment['lease_id'];
            $paymentID = $payment['id'];
            do {
                $invoice = $this->invoiceRepository->oldestUnpaid($leaseID);
                if (!isset($invoice))
                    break;
                do {
                    $invoiceID = $invoice['id'];
                    $unpaidItem = $this->invoiceItemRepository->oldestPendingItemByInvoiceID($invoiceID);
                    if (!isset($unpaidItem) && $invoice['paid_on'] == null) {
                        // Invoice is fully paid
                        $this->invoiceRepository->update(
                            ['paid_on' => $today],
                            $invoiceID
                        );
                        // Extra payment
                        if ($wallet > 0) {
                            $this->journalRepository->earnPrepayment([
                                'narration'     => 'Pre Payments '. $payment['receipt_number'],
                                'property_id'   => $payment['property_id'],
                                'amount'        => $wallet,
                                'reference_id'  => $payment['id'],
                                'lease_number'  => $payment['lease_number'],
                                'created_by'    => $payment['created_by']
                            ]);
                            $wallet = 0;
                        }
                        break;
                    }

                    $amountAlreadyPaidForItem = $this->transactionRepository->itemPaidAmount($unpaidItem['id']);
                    $itemBalance = $unpaidItem['amount'] - $amountAlreadyPaidForItem;

                    $transactionAmount = transaction_amount($wallet, $itemBalance);
                    $wallet = $wallet - $transactionAmount;

                    $this->transactionRepository->create([
                        'invoice_id'            => $unpaidItem['invoice_id'],
                        'invoice_item_id'       => $unpaidItem['id'],
                        'payment_id'            => $paymentID,
                        'transaction_date'      => $today,
                        'transaction_amount'    => $transactionAmount,
                        'invoice_item_type'     => $unpaidItem['item_type'],
                        'transaction_type'      => PAYMENT
                    ]);

                    // If it was a prepayment, less it from suspense account
                    if (!$newPayment) {
                        $this->journalRepository->fromPrepayment([
                            'narration'     => 'Spent Pre-Payments '. $payment['receipt_number'],
                            'property_id'   => $payment['property_id'],
                            'amount'        => $transactionAmount,
                            'reference_id'  => $payment['id'],
                            'lease_number'  => $payment['lease_number'],
                            'created_by'    => $payment['created_by']
                        ]);
                    }

                    // Item is fully paid
                    if ($transactionAmount == $itemBalance) {
                        $fullyPaidItems[] = $unpaidItem['id'];
                        $this->invoiceItemRepository->update(
                            ['paid_on' => $today],
                            $unpaidItem['id']
                        );
                    }

                    // Invoice is fully paid
                    $anyPendingItem = $this->invoiceItemRepository->oldestPendingItemByInvoiceID($invoiceID);
                    if (!isset($anyPendingItem) && $invoice['paid_on'] == null) {
                        $this->invoiceRepository->update(
                            ['paid_on' => $today],
                            $invoiceID
                        );
                    }
                } while ($wallet > 0);
            } while ($wallet > 0);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::info(json_encode('- ERROR - pay - '));
            throw new \Exception($exception->getMessage());
        }
    }
}
