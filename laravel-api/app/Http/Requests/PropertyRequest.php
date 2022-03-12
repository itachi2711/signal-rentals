<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 13/05/2020
 * Time: 14:07
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PropertyRequest extends BaseRequest
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
                      //  'agent_id' => 'exists:agents,id',
                        'property_code'          => 'required|unique:properties,property_code,NULL,id,deleted_at,NULL',
                        'landlord_id'=> '',
                        'property_type_id'=> '', // e.g apartment, commercial, duplex, house, mixed_use, other
                        'location'=> '',
                        'latitude'=> '',
                        'longitude'=> '',
                        'address_1'=> '',
                        'address_2'=> '',
                        'country'=> '',
                        'state'=> '',
                        'city'=> '',
                        'zip'=> '',
                        'late_fees_type'=> '',
                        'late_fees_value'=> '',
                        'created_by'=> '',
                        'updated_by'=> '',
                        'deleted_by'=> ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'property_code' => ['required', Rule::unique('properties')->ignore($this->property, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'property_name'             => 'required',
                        'property_status'           => '',
                        'property_type_id'          => 'required|exists:property_types,id', // e.g apartment, commercial, duplex, house, mixed_use, other
                        'location'                  => 'required',
                        'latitude'                  => 'nullable',
                        'longitude'                 => 'nullable',
                        'address_1'                 => 'nullable',
                        'address_2'                 => 'nullable',
                        'country'                   => 'nullable',
                        'state'                     => 'nullable',
                        'city'                      => 'nullable',
                        'zip'                       => 'nullable',
                        'agent_commission_value'    => 'nullable',
                        'agent_commission_type'     => 'nullable',
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
