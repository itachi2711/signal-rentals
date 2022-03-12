<?php

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\UnitUtilityBillInterface;
use App\Models\UnitUtilityBill;

class UnitUtilityBillRepository extends BaseRepository implements UnitUtilityBillInterface
{
    protected $model;

    /**
     * UnitUnitUtilityBillRepository constructor.
     * @param UnitUtilityBill $model
     */
    function __construct(UnitUtilityBill $model)
    {
        $this->model = $model;
    }

}
