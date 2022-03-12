<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 13/05/2020
 * Time: 14:44
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'leases_total'      => $this->leases_total,
            'agent_id'          => $this->agent_id,
            'property_id'       => $this->property_id,
            'property'            => collect($this->property)
                ->intersectByKeys(
                    [
                        'property_name' => '',
                        'property_code' => '',
                        'location'      => '',
                        'id'            => ''
                    ]
                ),

            'unit_name'         => $this->unit_name,
            'rent_amount'       => $this->rent_amount,
            'unit_floor'        => $this->unit_floor,
            'description'       => $this->description,
            'rent_period'       => $this->rent_period,
            'unit_status'       => $this->unit_status,
            'billing_frequency' => $this->billing_frequency,
            'unit_mode'         => $this->unit_mode,
            'unit_type_id'      => $this->unit_type_id,
            'unit_type'            => collect($this->unit_type)
                ->intersectByKeys(
                    [
                        'unit_type_display_name' => '',
                        'id' => ''
                    ]
                ),
            'lease_numbers'     => $this->leases->implode('lease_number', ', '),
            'bath_rooms'        => $this->bath_rooms,
            'bed_rooms'         => $this->bed_rooms,
            'total_rooms'       => $this->total_rooms,
            'square_foot'       => $this->square_foot,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'readings'          => ReadingResource::collection($this->whenLoaded('readings')),
            'leases'            => LeaseResource::collection($this->whenLoaded('leases')),
        ];
    }
}
