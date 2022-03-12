<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:23 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\EmailTemplateInterface;
use App\Models\EmailTemplate;

class EmailTemplateRepository extends BaseRepository implements EmailTemplateInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param EmailTemplate $model
     */
    function __construct(EmailTemplate $model)
    {
        $this->model = $model;
    }
}
