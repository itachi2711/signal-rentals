<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/20/2021
 * Time: 12:37 PM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ReadingResource extends JsonResource
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
            'unit' => $this->unit,

            'property_id' => $this->property_id,
            'property' => $this->property,

            'utility_id' => $this->utility_id,
            'utility' => $this->utility,

            'reading_date' => $this->reading_date,
            'current_reading' => $this->current_reading,

            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
