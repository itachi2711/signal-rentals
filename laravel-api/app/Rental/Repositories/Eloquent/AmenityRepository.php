<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 15/05/2020
 * Time: 23:07
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\AmenityInterface;
use App\Models\Amenity;

class AmenityRepository extends BaseRepository implements AmenityInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Amenity $model
     */
    function __construct(Amenity $model)
    {
        $this->model = $model;
    }


    /**
     * As an entity used for drop down select, we load all possible values. 100 is large enough guess for a max records
     * @return int
     */
    public function limit()
    {
        return (int)(request()->query('limit')) ? : 100;
    }

}
