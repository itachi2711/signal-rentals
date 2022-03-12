<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:06 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\CommunicationSetting;
use App\Rental\Repositories\Contracts\CommunicationSettingInterface;

class CommunicationSettingRepository extends BaseRepository implements CommunicationSettingInterface
{

    protected $model;

    /**
     * CommunicationSettingRepository constructor.
     * @param CommunicationSetting $model
     */
    function __construct(CommunicationSetting $model)
    {
        $this->model = $model;
    }

}
