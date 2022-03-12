<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:16 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\EmailConfigSettingInterface;
use App\Models\EmailConfigSetting;

class EmailConfigSettingRepository extends BaseRepository implements EmailConfigSettingInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param EmailConfigSetting $model
     */
    function __construct(EmailConfigSetting $model)
    {
        $this->model = $model;
    }

}
