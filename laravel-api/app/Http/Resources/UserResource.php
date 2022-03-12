<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'role_id'           => $this->role_id,
            'role'              => RoleResource::make($this->role),
            'first_name'        => $this->first_name,
            'middle_name'        => $this->middle_name,
            'last_name'         => $this->last_name,
            'photo'                 => $this->photo,
            'postal_code'           => $this->postal_code,
            'postal_address'        => $this->postal_address,
            'physical_address'      => $this->physical_address,
            'city'                  => $this->city,
            'state'                  => $this->state,
            'country'               => $this->country,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'confirmed'         => $this->confirmed,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
