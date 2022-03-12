<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/29/2020
 * Time: 10:44 AM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\InvoiceItemInterface;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceItemRepository extends BaseRepository implements InvoiceItemInterface
{
    protected $model;

    /**
     * InvoiceItemRepository constructor.
     * @param InvoiceItem $model
     */
    function __construct(InvoiceItem $model)
    {
        $this->model = $model;
    }

    /**
     * @param $date
     * @param $lease
     * @param array $itemData
     */
    public function item($date, $lease, $itemData = []) {
        $this->model->create(array_merge($itemData, [
            'lease_id'          => $lease['id'],
            'property_id'       => $lease['property_id'],
        ]));
    }

    /**
     * @param $invoiceID
     * @return mixed
     */
    public function oldestPendingItemByInvoiceID($invoiceID)
    {
        return $this->model
            ->where('invoice_id', $invoiceID)
            ->where('paid_on', null)
            ->oldest()
            ->first();
    }

    /**
     * @param $invoiceID
     * @return mixed
     */
    public function rentAmount($invoiceID)
    {
        return DB::table('invoice_items')
            ->select(DB::raw('COALESCE(sum(invoice_items.amount), 0.0) as rentAmount'))
            ->where('invoice_id', $invoiceID)
            ->where('item_type', ITEM_RENT)
            ->first()->rentAmount;
    }
}
