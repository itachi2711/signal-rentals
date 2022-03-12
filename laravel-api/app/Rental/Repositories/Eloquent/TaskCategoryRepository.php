<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/15/2021
 * Time: 4:42 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\TaskCategory;
use App\Rental\Repositories\Contracts\TaskCategoryInterface;

class TaskCategoryRepository extends BaseRepository implements TaskCategoryInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param TaskCategory $model
     */
    function __construct(TaskCategory $model)
    {
        $this->model = $model;
    }
}
