<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 2/3/2021
 * Time: 7:25 AM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\Task;
use App\Rental\Repositories\Contracts\TaskInterface;

class TaskRepository extends BaseRepository implements TaskInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Task $model
     */
    function __construct(Task $model)
    {
        $this->model = $model;
    }
}
