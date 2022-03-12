<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 28/05/2020
 * Time: 21:53
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\UtilityBillInterface;
use App\Models\UtilityBill;

class UtilityBillRepository extends BaseRepository implements UtilityBillInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param UtilityBill $model
     */
    function __construct(UtilityBill $model)
    {
        $this->model = $model;
    }

}
