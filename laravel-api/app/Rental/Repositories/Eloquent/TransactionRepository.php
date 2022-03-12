<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/30/2021
 * Time: 5:01 AM
 */

namespace App\Rental\Repositories\Eloquent;


use App\Rental\Repositories\Contracts\TransactionInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseRepository implements TransactionInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Transaction $model
     */
    function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    /**
     * @param $invoiceItemID
     * @return mixed
     */
    public function itemPaidAmount($invoiceItemID) {
        return DB::table('transactions')
            ->select(DB::raw('COALESCE(sum(transactions.transaction_amount), 0.0) as totalPaid'))
            ->where('invoice_item_id', $invoiceItemID)
            ->first()->totalPaid;
    }

    /**
     * @param $invoiceID
     * @return mixed
     */
    public function invoicePaidAmount($invoiceID) {
        return DB::table('transactions')
            ->select(DB::raw('COALESCE(sum(transactions.transaction_amount), 0.0) as totalPaid'))
            ->where('invoice_id', $invoiceID)
            ->first()->totalPaid;
    }

    /**
     * Amount paid for rent, for this invoice
     * @param $invoiceID
     * @return mixed
     */
    public function paidRent($invoiceID)
    {
        return DB::table('transactions')
            ->select(DB::raw('COALESCE(sum(transactions.transaction_amount), 0.0) as rentPaid'))
            ->where('invoice_id', $invoiceID)
            ->where('transaction_type', ITEM_RENT)
            ->first()->rentPaid;
    }
}
