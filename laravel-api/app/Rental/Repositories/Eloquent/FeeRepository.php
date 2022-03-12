<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 15/05/2020
 * Time: 23:14
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\FeeInterface;
use App\Models\Fee;

class FeeRepository extends BaseRepository implements FeeInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Fee $model
     */
    function __construct(Fee $model)
    {
        $this->model = $model;
    }

}
