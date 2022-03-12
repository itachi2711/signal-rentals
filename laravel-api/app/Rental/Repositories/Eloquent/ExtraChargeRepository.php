<?php


namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\ExtraChargeInterface;
use App\Models\ExtraCharge;

class ExtraChargeRepository extends BaseRepository implements ExtraChargeInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param ExtraCharge $model
     */
    function __construct(ExtraCharge $model)
    {
        $this->model = $model;
    }

    /**
     * As an entity used for drop down select, we load all possible values. 100 is large enough guess for a max records
     * @return int
     */
    public function limit()
    {
        return (int)(request()->query('limit')) ?: 100;
    }

    /**
     * @return string
     */
    public function sortField()
    {
        return (string)(request()->query('sortField')) ?: 'extra_charge_display_name';
    }

}
