<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/10/2021
 * Time: 8:38 AM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\AgentInterface;
use App\Models\Agent;

class AgentRepository extends BaseRepository implements AgentInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Agent $model
     */
    function __construct(Agent $model)
    {
        $this->model = $model;
    }

}
