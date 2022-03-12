<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/15/2021
 * Time: 4:52 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\VacationNoticeInterface;
use App\Models\VacationNotice;

class VacationNoticeRepository extends BaseRepository implements VacationNoticeInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param VacationNotice $model
     */
    function __construct(VacationNotice $model)
    {
        $this->model = $model;
    }
}

