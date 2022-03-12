<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 22/05/2020
 * Time: 13:30
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\LeaseTypeInterface;
use App\Models\LeaseType;

class LeaseTypeRepository extends BaseRepository implements LeaseTypeInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param LeaseType $model
     */
    function __construct(LeaseType $model)
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
        return (string)(request()->query('sortField')) ? : 'lease_type_display_name';
    }

}
