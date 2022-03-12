<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 22/05/2020
 * Time: 17:59
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\PaymentFrequencyInterface;
use App\Models\PaymentFrequency;

class PaymentFrequencyRepository extends BaseRepository implements PaymentFrequencyInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param PaymentFrequency $model
     */
    function __construct(PaymentFrequency $model)
    {
        $this->model = $model;
    }

    /**
     * As an entity used for drop down select, we load all possible values. 100 is large enough guess for a max records
     * @return int
     */
    public function limit()
    {
        return (int)(request()->query('limit')) ? : 100;
    }

    /**
     * @return string
     */
    public function sortField ()
    {
        return (string)(request()->query('sortField')) ? : 'payment_frequency_display_name';
    }

}
