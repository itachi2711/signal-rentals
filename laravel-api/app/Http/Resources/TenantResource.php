<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 13/05/2020
 * Time: 14:28
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TenantResource extends JsonResource
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

            'agent_id'=> $this->agent_id,
            'tenant_type_id'=> $this->tenant_type_id,
            'first_name'=> $this->first_name,
            'middle_name'=> $this->middle_name,
            'last_name'=> $this->last_name,
            'gender'=> $this->gender,
            'date_of_birth'=> $this->date_of_birth,
            'date_of_birth_display'=> format_date($this->date_of_birth),
            'id_passport_number'=> $this->id_passport_number,
            'marital_status'=> $this->marital_status,
            'tenant_number'=> $this->tenant_number,

            'phone' => $this->phone,
            'email' => $this->email,
            'country' => $this->country,
            'city' => $this->city,
            'state' => $this->state,

            'postal_code' => $this->postal_code,
            'postal_address' => $this->postal_address,
            'physical_address' => $this->physical_address,

			'leases' => LeaseResource::collection($this->whenLoaded('leases')),
			//'leases' => LeaseResource::collection($this->leases),

            'business_name' => $this->business_name,
            'registration_number' => $this->registration_number,
            'business_industry' => $this->business_industry,
            'business_description' => $this->business_description,
            'business_address' => $this->business_address,

            'next_of_kin_name' => $this->next_of_kin_name,
            'next_of_kin_phone' => $this->next_of_kin_phone,
            'next_of_kin_relation' => $this->next_of_kin_relation,

            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'emergency_contact_email' => $this->emergency_contact_email,
            'emergency_contact_relationship' => $this->emergency_contact_relationship,
            'emergency_contact_postal_address' => $this->emergency_contact_postal_address,
            'emergency_contact_physical_address' => $this->emergency_contact_physical_address,

            'employment_status' => $this->employment_status,
            'employment_position' => $this->employment_position,
            'employer_contact_phone' => $this->employer_contact_phone,
            'employer_contact_email' => $this->employer_contact_email,
            'employment_postal_address' => $this->employment_postal_address,
            'employment_physical_address' => $this->employment_physical_address,

            'rent_payment_contact' => $this->rent_payment_contact,
            'rent_payment_contact_postal_address' => $this->rent_payment_contact_postal_address,
            'rent_payment_contact_physical_address' => $this->rent_payment_contact_physical_address,

            'profile_pic' => $this->profile_pic,
            'password_set' => $this->password_set,
            'confirmed' => $this->confirmed,
            'confirmation_code' => $this->confirmation_code,

            'created_by'=> $this->created_by,
            'updated_by'=> $this->updated_by,
            'deleted_by'=> $this->deleted_by,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
