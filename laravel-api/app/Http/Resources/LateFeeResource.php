<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/27/2021
 * Time: 9:55 AM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LateFeeResource extends JsonResource
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
            'late_fee_name'         => $this->late_fee_name,
            'late_fee_display_name' => $this->late_fee_display_name,
            'late_fee_description'  => $this->late_fee_description,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}

