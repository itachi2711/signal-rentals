<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 22/05/2020
 * Time: 13:34
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaseModeResource extends JsonResource
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

            'lease_mode_name' => $this->lease_mode_name,
            'lease_mode_display_name' => $this->lease_mode_display_name,
            'lease_mode_description' => $this->lease_mode_description,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
