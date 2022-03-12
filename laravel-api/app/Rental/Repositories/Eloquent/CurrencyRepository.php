<?php

namespace App\Rental\Repositories\Eloquent;

use App\Models\Currency;
use App\Rental\Repositories\Contracts\CurrencyInterface;

class CurrencyRepository extends BaseRepository implements CurrencyInterface {

    protected $model;

    /**
     * CurrencyRepository constructor.
     * @param Currency $model
     */
    function __construct(Currency $model)
    {
        $this->model = $model;
    }

}
