<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/31/2020
 * Time: 10:22 AM
 */

namespace App\Rental\Repositories\Contracts;

interface LedgerInterface extends BaseInterface
{
    /**
     * @param $journalId
     * @return mixed
     */
    function ledgerEntry($journalId);
}
