<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/15/2021
 * Time: 4:42 PM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskCategoryResource extends JsonResource
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

            'category_name'         => $this->category_name,
            'category_description'  => $this->category_description,

            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at
        ];
    }
}
