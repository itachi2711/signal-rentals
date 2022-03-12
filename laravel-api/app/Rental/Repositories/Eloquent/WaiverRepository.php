<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/30/2021
 * Time: 5:08 AM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\InvoiceItemInterface;
use App\Rental\Repositories\Contracts\JournalInterface;
use App\Rental\Repositories\Contracts\TransactionInterface;
use App\Rental\Repositories\Contracts\WaiverInterface;
use App\Models\Waiver;
use Illuminate\Support\Facades\Log;

class WaiverRepository extends BaseRepository implements WaiverInterface
{
    protected $model, $invoiceRepository, $invoiceItemRepository, $journalRepository, $transactionRepository;

    /**
     * WaiverRepository constructor.
     * @param Waiver $model
     * @param InvoiceItemInterface $invoiceItemRepository
     * @param InvoiceInterface $invoiceRepository
     * @param JournalInterface $journalRepository
     * @param TransactionInterface $transactionRepository
     */
    function __construct(Waiver $model, InvoiceItemInterface $invoiceItemRepository,
                         InvoiceInterface $invoiceRepository,
                         JournalInterface $journalRepository, TransactionInterface $transactionRepository)
    {
        $this->model = $model;
        $this->invoiceItemRepository = $invoiceItemRepository;
        $this->journalRepository = $journalRepository;
        $this->transactionRepository = $transactionRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @param $data
     */
    public function processWaiver($data)
    {
        $this->journalRepository->receiveWaiver([
            'narration'     => 'Bill Waived',
            'property_id'   => $data['property_id'],
            'amount'        => $data['amount'],
            'reference_id'  => $data['id'],
            'lease_number'  => $data['lease_number'],
            'created_by'    => $data['created_by']
        ]);

        $waiverID = $data['id'];
        $waiverWallet = $data['amount'];
        $invoiceID = $data['invoice_id'];
        $waiverDate = date('Y-m-d');

        do {
            $invoice = $this->invoiceRepository->getById($invoiceID);
            if (!isset($invoice))
                break;
            do {
                $unpaidItem = $this->invoiceItemRepository->oldestPendingItemByInvoiceID($invoiceID);
                if (!isset($unpaidItem) && $invoice['paid_on'] == null) {
                    $this->invoiceRepository->update(
                        ['paid_on' => $waiverDate],
                        $invoiceID
                    );
                    break;
                }
                $amountAlreadyPaidForItem = $this->transactionRepository->itemPaidAmount($unpaidItem['id']);
                $itemBalance = $unpaidItem['amount'] - $amountAlreadyPaidForItem;

                $transactionAmount = transaction_amount($waiverWallet, $itemBalance);
                $waiverWallet = $waiverWallet - $transactionAmount;

                $this->transactionRepository->create([
                    'invoice_id'            => $unpaidItem['invoice_id'],
                    'invoice_item_id'       => $unpaidItem['id'],
                    'waiver_id'             => $waiverID,
                    'transaction_date'      => $waiverDate,
                    'transaction_amount'    => $transactionAmount,
                    'invoice_item_type'     => $unpaidItem['item_type'],
                    'transaction_type'      => WAIVER
                ]);

                // Item is fully paid
                if ($transactionAmount == $itemBalance) {
                    $fullyPaidItems[] = $unpaidItem['id'];
                    $this->invoiceItemRepository->update(
                        ['paid_on' => $waiverDate],
                        $unpaidItem['id']
                    );
                }

                $anyPendingItem = $this->invoiceItemRepository->oldestPendingItemByInvoiceID($invoiceID);
                if (!isset($anyPendingItem) && $invoice['paid_on'] == null) {
                    $this->invoiceRepository->update(
                        ['paid_on' => $waiverDate],
                        $invoiceID
                    );
                }
            } while ($waiverWallet > 0);
        } while ($waiverWallet > 0);
    }
}

