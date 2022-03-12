<?php
/**
 * Created by PhpStorm.
 * Permission: kevin
 * Date: 26/10/2018
 * Time: 21:54
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\PermissionInterface;
use App\Models\Permission;

class PermissionRepository extends BaseRepository implements PermissionInterface {

    protected $model;

    protected $orderBy  = array('display_name', 'desc');

    /**
     * PermissionRepository constructor.
     * @param Permission $model
     */
    function __construct(Permission $model)
    {
        $this->model = $model;
    }
}
