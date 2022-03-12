<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:13 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\TenantSetting;
use App\Rental\Repositories\Contracts\TenantSettingInterface;

class TenantSettingRepository extends BaseRepository implements TenantSettingInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param TenantSetting $model
     */
    function __construct(TenantSetting $model)
    {
        $this->model = $model;
    }

}
