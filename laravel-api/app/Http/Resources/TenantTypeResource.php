<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 22/05/2020
 * Time: 13:06
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantTypeResource extends JsonResource
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

            'tenant_type_name' => $this->tenant_type_name,
            'tenant_type_display_name' => $this->tenant_type_display_name,
            'tenant_type_description' => $this->tenant_type_description,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
