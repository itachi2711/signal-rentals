<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 05/05/2020
 * Time: 01:05
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\TenantInterface;
use App\Models\Tenant;

class TenantRepository extends BaseRepository implements TenantInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Tenant $model
     */
    function __construct(Tenant $model)
    {
        $this->model = $model;
    }

    /**
     * @param $email
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    function getViaEmail($email) {
        return $this->model->where('email', $email)
            ->orderBy('updated_at', 'desc')
            ->first();
    }

}
