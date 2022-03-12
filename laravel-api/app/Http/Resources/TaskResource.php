<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 2/3/2021
 * Time: 7:31 AM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title'=> $this->title,
            'date'=> $this->date,
            'lease_id'=> $this->lease_id,
            'property_id'=> $this->property_id,
            'unit_id'=> $this->unit_id,
            'tenant_id'=> $this->tenant_id,
            'task_category'=> $this->task_category,
            'priority'=> $this->priority,
            'status'=> $this->status,
            'description'=> $this->description
        ];
    }
}
