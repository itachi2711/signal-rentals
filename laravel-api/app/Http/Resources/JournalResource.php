<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/29/2020
 * Time: 11:54 AM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class JournalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'agent_id'          => $this->agent_id,
            'property_id'       => $this->property_id,

            'reference_id'      => $this->reference_id,
            'debit_account_id'  => $this->debit_account_id,
            'credit_account_id' => $this->credit_account_id,
            'amount'            => format_money($this->amount),
            'narration'         => $this->narration,
            'preparedBy'        => $this->preparedBy,

            'debitAccount'      => AccountResource::make($this->debitAccount),
            'creditAccount'     => AccountResource::make($this->creditAccount),

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,
            'created_at'        => format_date($this->created_at),
            'updated_at'        => $this->updated_at
        ];
    }
}
