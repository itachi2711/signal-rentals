<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitUtilityBillResource extends JsonResource
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

            'agent_id' => $this->agent_id,
            'unit_id' => $this->unit_id,
            'utility_bill_id' => $this->utility_bill_id,

            'reading_date' => $this->reading_date,
            'current_reading' => $this->current_reading,

            'property_id' => $this->property_id,
            'utility_id' => $this->utility_id,

            'agent'         => $this->agent,
            'property'      => $this->property,
            'utility'       => $this->utility,
            'unit'          => $this->unit,
            'utility_bill'  => $this->utility_bill,

            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
