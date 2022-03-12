<?php
/**
 * Created by PhpStorm.
 * PropertyType: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 14/04/2020
 * Time: 13:10
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\PropertyTypeInterface;
use App\Models\PropertyType;

class PropertyTypeRepository extends BaseRepository implements PropertyTypeInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param PropertyType $model
     */
    function __construct(PropertyType $model)
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

    /**
     * @return string
     */
    public function sortField ()
    {
        return (string)(request()->query('sortField')) ? : 'display_name';
    }

}
