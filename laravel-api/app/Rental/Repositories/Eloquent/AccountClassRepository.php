<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:20
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\AccountClass;
use App\Rental\Repositories\Contracts\AccountClassInterface;

class AccountClassRepository extends BaseRepository implements AccountClassInterface
{

    protected $model;

    /**
     * AccountRepository constructor.
     * @param AccountClass $model
     */
    function __construct(AccountClass $model)
    {
        $this->model = $model;
    }

}
