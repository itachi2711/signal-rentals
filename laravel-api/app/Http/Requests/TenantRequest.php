<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 13/05/2020
 * Time: 14:28
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class TenantRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                    break;
                }
            case 'POST':
                {
                    $rules = [
                        'agent_id' => 'nullable|exists:agents,id',
                        'tenant_type_id'=> '',
                        'first_name'=> '',
                        'middle_name'=> '',
                        'last_name'=> '',
                        'gender'=> '',
                        'date_of_birth'=> '',
                        'id_passport_number'=> '',
                        'marital_status'=> '',

                        'phone'                 => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|unique:tenants,phone,NULL,id,deleted_at,NULL',
                        'email'      => 'required|email|unique:tenants,email,NULL,id,deleted_at,NULL',
                        'country'=> '',
                        'state'=> '',
                        'city'=> '',

                        'postal_code'=> '',
                        'postal_address'=> '',
                        'physical_address'=> '',

                        'business_name'=> '',
                        'registration_number'=> '',
                        'business_industry'=> '',
                        'business_description'=> '',
                        'business_address'=> '',

                        'next_of_kin_name'=> '',
                        'next_of_kin_phone'=> '',
                        'next_of_kin_relation'=> '',

                        'emergency_contact_name'=> '',
                        'emergency_contact_phone'=> '',
                        'emergency_contact_email'=> '',
                        'emergency_contact_relationship'=> '',
                        'emergency_contact_postal_address'=> '',
                        'emergency_contact_physical_address'=> '',

                        'employment_status'=> '',
                        'employment_position'=> '',
                        'employer_contact_phone'=> '',
                        'employer_contact_email'=> '',
                        'employment_postal_address'=> '',
                        'employment_physical_address'=> '',

                        'rent_payment_contact'=> '',
                        'rent_payment_contact_postal_address'=> '',
                        'rent_payment_contact_physical_address'=> '',

                        'profile_pic'=> '',
                        'password_set'=> '',
                        'confirmed'=> '',
                        'confirmation_code'=> '',

                        'password'              => 'nullable|min:3|confirmed',
                        'password_confirmation' => 'required_with:password',

                        'created_by'=> '',
                        'updated_by'=> '',
                        'deleted_by'
                    ];
                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'agent_id' => 'nullable',
                        'tenant_type_id'=> '',
                        'first_name'=> '',
                        'middle_name'=> '',
                        'last_name'=> '',
                        'gender'=> '',
                        'date_of_birth'=> '',
                        'id_passport_number'=> '',
                        'marital_status'=> '',
                        'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/',
                            Rule::unique('tenants')->ignore($this->tenant, 'id')
                                ->where(function ($query) {
                                    $query->where('deleted_at', NULL);
                                })],
                        'email' => ['required', 'email', Rule::unique('tenants')->ignore($this->tenant, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'country'=> '',
                        'state'=> '',
                        'city'=> '',

                        'postal_code'=> '',
                        'postal_address'=> '',
                        'physical_address'=> '',

                        'business_name'=> '',
                        'registration_number'=> '',
                        'business_industry'=> '',
                        'business_description'=> '',
                        'business_address'=> '',

                        'next_of_kin_name'=> '',
                        'next_of_kin_phone'=> '',
                        'next_of_kin_relation'=> '',

                        'emergency_contact_name'=> '',
                        'emergency_contact_phone'=> '',
                        'emergency_contact_email'=> '',
                        'emergency_contact_relationship'=> '',
                        'emergency_contact_postal_address'=> '',
                        'emergency_contact_physical_address'=> '',

                        'employment_status'=> '',
                        'employment_position'=> '',
                        'employer_contact_phone'=> '',
                        'employer_contact_email'=> '',
                        'employment_postal_address'=> '',
                        'employment_physical_address'=> '',

                        'rent_payment_contact'=> '',
                        'rent_payment_contact_postal_address'=> '',
                        'rent_payment_contact_physical_address'=> '',

                        'profile_pic'=> '',
                        'password_set'=> '',
                        'confirmed'=> '',
                        'confirmation_code'=> '',

                        'password'              => 'nullable|min:3|confirmed',
                        'password_confirmation' => 'required_with:password',

                        'created_by'=> '',
                        'updated_by'=> '',
                        'deleted_by'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
