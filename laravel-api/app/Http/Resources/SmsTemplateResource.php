<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:28 PM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsTemplateResource extends JsonResource
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

            'name' => $this->name,
            'display_name' => $this->display_name,
            'body' => $this->body,
            'tags' => $this->tags,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
