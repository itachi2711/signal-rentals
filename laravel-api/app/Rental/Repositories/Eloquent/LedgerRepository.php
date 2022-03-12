<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/31/2020
 * Time: 10:22 AM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\LedgerInterface;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;

class LedgerRepository extends BaseRepository implements LedgerInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Ledger $model
     */
    function __construct(Ledger $model)
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

        DB::table('ledgers')->insertUsing(['account_id','journal_id','created_at','amount'], $unitedQuery);
    }

}
