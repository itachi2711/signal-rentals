<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 07/05/2020
 * Time: 12:16
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LandlordResource extends JsonResource
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
            'id'                        => $this->id,
            'agent_id'                  => $this->agent_id,
            'agent'                     => $this->agent,
            'first_name'                => $this->first_name,
            'middle_name'               => $this->middle_name,
            'last_name'                 => $this->last_name,
            'phone'                     => $this->phone,
            'email'                     => $this->email,
            'registration_date'         => $this->registration_date,
            'registration_date_display' => format_date($this->registration_date),
            'id_number'                 => $this->id_number,
            'country'                   => $this->country,
            'state'                     => $this->state,
            'city'                      => $this->city,
            'postal_address'            => $this->postal_address,
            'physical_address'          => $this->physical_address,
            'residential_address'       => $this->residential_address,
            'confirmed'                 => $this->confirmed,

            'property_total'            => $this->property_total,
            'unit_total'                => $this->unit_total,
            'properties'                => LandlordResource::collection($this->whenLoaded('properties')),

            'created_by'                => $this->created_by,
            'updated_by'                => $this->updated_by,
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at
        ];
    }
}
