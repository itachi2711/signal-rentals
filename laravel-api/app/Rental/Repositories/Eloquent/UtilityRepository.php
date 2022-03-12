<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 15/05/2020
 * Time: 23:03
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\UtilityInterface;
use App\Models\Utility;

class UtilityRepository extends BaseRepository implements UtilityInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Utility $model
     */
    function __construct(Utility $model)
    {
        $this->model = $model;
    }

    /**
     * As an entity used for drop down select, we load all possible values. 100 is large enough guess for a max records
     * @return int
     */
    public function limit()
    {
        return (int)(request()->query('limit')) ? : 3;
    }

}
