<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/20/2021
 * Time: 12:37 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\ReadingInterface;
use App\Models\Reading;

class ReadingRepository extends BaseRepository implements ReadingInterface
{

    protected $model;

    /**
     * ReadingRepository constructor.
     * @param Reading $model
     */
    function __construct(Reading $model)
    {
        $this->model = $model;
    }

    /**
     * @param $unitID
     * @param $utilityID
     * @return mixed
     */
    public function getLastReading($unitID, $utilityID) {
       return  $this->model
            ->where('utility_id', $utilityID)
            ->where('unit_id', $unitID)
            ->latest()->first();
    }

    /**
     * @param $utilityID
     * @param $previousBillingDate
     * @return |null
     * @throws \Exception
     */
    public function getPreviousReading($utilityID, $previousBillingDate) {
        if(!isset($utilityID) || !isset($previousBillingDate))
            throw new \Exception('ReadingRepository -> getCurrentReading() Null values provided');
        $reading = $this->model
            ->where('utility_id', $utilityID)
            ->where('reading_date', '<=', $previousBillingDate)
            ->latest()->first();
        return  isset($reading) ? $reading['current_reading'] : null;
    }

    /**
     * @param $utilityID
     * @param $currentBillingDate
     * @return |null
     * @throws \Exception
     */
    public function getCurrentReading($utilityID, $currentBillingDate) {
        //the most recent reading that equal or less that today ()
        if(!isset($utilityID) || !isset($currentBillingDate))
            throw new \Exception('ReadingRepository -> getCurrentReading() Null values provided');

        $reading = $this->model
            ->where('utility_id', $utilityID)
            ->where('reading_date', '<=', $currentBillingDate)
            ->latest()->first();
        return  isset($reading) ? $reading['current_reading'] : null;
    }

    /**
     * @param $utilityID
     * @param $currentBillingDate
     * @param $previousBillingDate
     * @return |null
     * @throws \Exception
     */
    public function periodicalUtilityConsumption($utilityID, $currentBillingDate, $previousBillingDate) {
        return $this->getCurrentReading($utilityID, $currentBillingDate) - $this->getPreviousReading($utilityID, $previousBillingDate);
    }
}
