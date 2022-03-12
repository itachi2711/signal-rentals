<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 20/10/2019
 * Time: 01:59
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\AccountLedger;
use App\Rental\Repositories\Contracts\AccountLedgerInterface;
use Illuminate\Support\Facades\DB;

class AccountLedgerRepository extends BaseRepository implements AccountLedgerInterface
{
    protected $model;

    /**
     * AccountLedgerRepository constructor.
     * @param AccountLedger $model
     */
    function __construct(AccountLedger $model)
    {
        $this->model = $model;
    }

    /**
     * Create an entry into the ledger table.
     * Ideally a db table should handle this.
     * Run into an issue (Prepared Statement needs to be re-prepared) with Laravel and MYSQl views, so figured to do it here
     * @param $journalId
     * @return mixed|void
     */
    public function ledgerEntry($journalId){

        $queryCredits =  DB::table('journals')
            ->select(DB::raw('journals.credit_account_id, journals.id, journals.created_at, (0.0 - journals.amount)'))
            ->where('journals.id', $journalId);

        $queryDebits =  DB::table('journals')
            ->select(DB::raw('journals.debit_account_id, journals.id, journals.created_at, journals.amount'))
            ->where('journals.id', $journalId);

        $unitedQuery = $queryDebits =  DB::table('journals')
            ->select(DB::raw('journals.debit_account_id, journals.id, journals.created_at, journals.amount'))
            ->where('journals.id', $journalId)
            ->unionAll($queryCredits);

        DB::table('account_ledgers')->insertUsing(['account_id','journal_id','created_at','amount'], $unitedQuery);

    }

}
