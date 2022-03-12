<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/10/2021
 * Time: 9:32 AM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\SystemNotification;
use App\Rental\Repositories\Contracts\SystemNotificationInterface;

class SystemNotificationRepository extends BaseRepository implements SystemNotificationInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param SystemNotification $model
     */
    function __construct(SystemNotification $model)
    {
        $this->model = $model;
    }
}
