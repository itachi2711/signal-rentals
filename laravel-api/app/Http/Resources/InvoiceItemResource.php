<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/30/2021
 * Time: 4:51 AM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
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
            'invoice_id'        => $this->invoice_id,
            'invoice'           => InvoiceResource::make($this->whenLoaded('invoice')),
            'lease_id'          => $this->lease_id,
            'lease'             => LeaseResource::make($this->whenLoaded('lease')),
            'property_id'       => $this->property_id,
            'property'          => PropertyResource::make($this->whenLoaded('property')),
            'item_name'         => $this->item_name,
            'item_type'         => $this->item_type,
            'item_description'  => $this->item_description,
            'quantity'          => $this->quantity,
            'price'             => $this->price,
            'amount'            => $this->amount,
            'discount'          => $this->discount,
            'paid_on'           => $this->paid_on,
            'tax'               => $this->tax,
            'tax_id'            => $this->tax_id,
            'transactions'      => $this->transactions,
            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
