<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 13/05/2020
 * Time: 14:07
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PropertyResource extends JsonResource
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
			// 'property_long_name' 	=> $this->property_name.' ('.$this->property_code.') - '.$this->location,
            'property_code'         => $this->property_code,
            'agent_id'              => $this->agent_id,
            'unit_total'            => $this->unit_total,
            'agent'                 => $this->agent,
            'landlord'              => LandlordResource::make($this->whenLoaded('landlord')),
            'property_type_id'      => $this->property_type_id,
            'property_type'            => collect($this->property_type)
                ->intersectByKeys(
                    ['display_name' => '',
                    'id' => '']
                ),
            'units'                 => UnitResource::collection($this->whenLoaded('units')),
            'vacant_units'          => UnitResource::collection($this->vacantUnits($this->units)),
			'total_units'           => count($this->units),
			'total_vacant_units'           => count($this->vacantUnits($this->units)),
          //  'extra_charges'         => ExtraChargeResource::collection($this->whenLoaded('extra_charges')),
            'extra_charges'         => $this->whenLoaded('extra_charges'),
            'late_fees'             => $this->whenLoaded('late_fees'),
            'utility_costs'         => $this->whenLoaded('utility_costs'),
            'payment_methods'       => PaymentMethodResource::collection($this->whenLoaded('payment_methods')),
            'periods'               => PeriodResource::collection($this->whenLoaded('periods')),
            'landlord_id'           => $this->landlord_id,
            'property_name'         => $this->property_name,
            'location'              => $this->location,
            'latitude'              => $this->latitude,
            'longitude'             => $this->longitude,
            'address_1'             => $this->address_1,
            'address_2'             => $this->address_2,
            'country'               => $this->country,
            'state'                 => $this->state,
            'city'                  => $this->city,
            'zip'                           => $this->zip,
            'agent_commission_value'        => $this->agent_commission_value,
            'agent_commission_type'         => $this->agent_commission_type,
            'created_by'    => $this->created_by,
            'updated_by'    => $this->updated_by,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }

    /**
     * Units with zero active leases
     * @param $units
     * @return array
     */
    private function vacantUnits($units)
    {
        $vacant = [];
        foreach ($units as $unit) {
            if ($unit['leases_total'] == 0)
                $vacant[] = $unit;
        }
        return $vacant;
    }
}
