<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/30/2021
 * Time: 5:07 AM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WaiverResource extends JsonResource
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
            'id'            => $this->id,

            'invoice_id'    => $this->invoice_id,
            'property_id'   => $this->property_id,
            'lease_id'      => $this->lease_id,
            'amount'        => $this->amount,
            'lease_number'  => $this->lease_number,

            'created_by'    => $this->created_by,
            'updated_by'    => $this->updated_by,
            'deleted_by'    => $this->deleted_by,

            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
