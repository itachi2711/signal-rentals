<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/30/2021
 * Time: 5:00 AM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'id'                    => $this->id,
            'agent_id'              => $this->agent_id,
            'invoice_id'            => $this->invoice_id,
            'invoice'               => $this->invoice,
            'invoice_item_id'       => $this->invoice_item_id,
            'invoice_item'          => $this->invoice_item,
            'payment_id'            => $this->payment_id,
            'payment'               => PaymentResource::make($this->whenLoaded('payment')),
            'waiver_id'            => $this->waiver_id,
            'waiver'               => WaiverResource::make($this->whenLoaded('waiver')),
            'transaction_date'      => $this->transaction_date,
            'transaction_amount'    => $this->transaction_amount,
            'invoice_item_type'     => $this->invoice_item_type,
            'transaction_type'      => $this->transaction_type,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
