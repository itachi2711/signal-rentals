<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/29/2020
 * Time: 12:12 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\AccountInterface;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountRepository extends BaseRepository implements AccountInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Account $model
     */
    function __construct(Account $model)
    {
        $this->model = $model;
    }

    /**
     * Fetch account statement
     * Uses incrementing journal_id to group.
     * This saves us trouble for cases with records having exact same timestamps.
     * @param $accountId
     * @return \Illuminate\Support\Collection
     */
    public function fetchAccountStatement($accountId) {
        return DB::table('ledgers as t1')
            ->where('t1.account_id', $accountId)
            ->leftJoin('journals', 't1.journal_id', '=', 'journals.id')
            ->leftJoin('invoices', 'journals.reference_id', '=', 'invoices.id')
            ->select(DB::raw(
                't1.account_id,
                t1.journal_id,
                t1.created_at,
                t1.amount,
                journals.narration,
                journals.reference_id,
                invoices.invoice_number,
                SUM(t2.amount) AS balance'
            ))
            ->join('ledgers AS t2', function($join){
                $join->on('t2.account_id', '=', 't1.account_id')
                    ->on('t2.journal_id', '<=', 't1.journal_id');
            })
            ->groupBy('t1.account_id', 't1.journal_id', 't1.amount')
            ->orderBy('t1.id', 'asc')
            ->get();
    }

    /**
     * @param $accountID
     * @return mixed
     */
    public function accountBalance($accountID) {
        return DB::table('ledgers')
            ->select(DB::raw('COALESCE(sum(ledgers.amount), 0.0) as balance'))
            ->where('ledgers.account_id', $accountID)
            ->first()->balance;
    }

    /**
     * @param $accountNumber
     * @return string | null
     */
    public function accountIDByAccountNumber($accountNumber)
    {
        $account = $this->model
            ->where('account_number', $accountNumber)
            ->first();
        if (!isset($account))
            return null;
        return $account['id'];
    }

    /**
     * @param $accountNumber
     * @return mixed|null
     */
    public function accountBalanceByAccountNumber($accountNumber)
    {
        $account = $this->model
            ->where('account_number', $accountNumber)
            ->first();
        if (!isset($account))
            return null;

        $accountID = $account['id'];
        return DB::table('ledgers')
            ->select(DB::raw('COALESCE(sum(ledgers.amount), 0.0) as balance'))
            ->where('ledgers.account_id', $accountID)
            ->first()->balance;
    }

}
