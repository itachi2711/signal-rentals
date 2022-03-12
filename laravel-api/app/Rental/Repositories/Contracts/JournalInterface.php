<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/29/2020
 * Time: 11:51 AM
 */

namespace App\Rental\Repositories\Contracts;

interface JournalInterface extends BaseInterface
{
    public function earnRentDeposit($data = []);
}
