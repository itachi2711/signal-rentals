<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/15/2021
 * Time: 4:52 PM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacationNoticeResource extends JsonResource
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
            'id' => $this->id,

            'agent_id'          => $this->agent_id,
            'tenant_id'         => $this->tenant_id,
            'tenant'            => $this->tenant,
            'lease_id'          => $this->lease_id,
            'lease'             => LeaseResource::make($this->lease),
            'property_id'       => $this->property_id,
            'property'          => $this->property,
            'unit'              => $this->unit,
            'date_received'     => format_date($this->date_received),
            'vacating_date_display'     => format_date($this->vacating_date),
            'vacating_date'     => $this->vacating_date,
            'vacating_reason'   => $this->vacating_reason,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

