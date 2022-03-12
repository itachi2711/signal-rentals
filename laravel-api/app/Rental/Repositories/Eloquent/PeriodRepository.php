<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/16/2021
 * Time: 7:27 AM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\PeriodInterface;
use App\Models\Period;
use Carbon\Carbon;

class PeriodRepository extends BaseRepository implements PeriodInterface
{
    protected $model;

    /**
     * PeriodRepository constructor.
     * @param Period $model
     */
    function __construct(Period $model)
    {
        $this->model = $model;
    }

    /**
     * @param $date
     * @param $lease
     * @return mixed
     */
    public function getPeriod($date, $lease)
    {
        /// For the first time we are billing,
        ///  so we bill for current period unless its to be skipped, in which we bill for next period
        ///  Otherwise we check if there is a setting to mark billing as for next period
        if (is_null($lease['billed_on'])) {
            $periodDate = Carbon::parse($date);
            if(isset($lease['skip_starting_period']) && $lease['skip_starting_period'] == true) {
                $periodDate = Carbon::parse($date)->addMonth();
            }
            return $this->firstOrCreate([
                'name' => date('F, Y', strtotime($periodDate))
            ]);
        }

        $nextPeriodBilling = $lease['next_period_billing'];

        $periodDate = $nextPeriodBilling ? Carbon::parse($date)->addMonth() : Carbon::parse($date);
        return $this->firstOrCreate([
            'name' => date('F, Y', strtotime($periodDate))
        ]);
    }
}
