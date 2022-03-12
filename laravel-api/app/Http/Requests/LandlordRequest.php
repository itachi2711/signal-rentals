<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 10/05/2020
 * Time: 15:53
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LandlordRequest extends BaseRequest
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
                        'agent_id'              => 'nullable',
                        'first_name'            => 'required',
                        'middle_name'           => 'nullable',
                        'last_name'             => 'nullable',
                        'phone'                 => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|unique:landlords,phone,NULL,id,deleted_at,NULL',
                        'email'                 => 'required|email|unique:landlords,email,NULL,id,deleted_at,NULL',
                        'registration_date'     => 'nullable',
                        'nationality'           => 'nullable',
                        'id_number'             => 'nullable',
                        'state'                 => 'nullable',
                        'city'                  => 'nullable',
                        'postal_address'        => 'nullable',
                        'physical_address'      => 'nullable',
                        'residential_address'   => 'nullable',

                        'confirmed'             => 'nullable',
                        'confirmation_code'     => 'nullable',

                        'password'              => 'nullable|min:3|confirmed',
                        'password_confirmation' => 'required_with:password',

                        'created_by'            => 'nullable',
                        'updated_by'            => 'nullable',
                        'deleted_by'            => 'nullable'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'agent_id'              => 'nullable',
                        'first_name'            => 'required',
                        'middle_name'           => 'nullable',
                        'last_name'             => '',
                        'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/',
                            Rule::unique('landlords')->ignore($this->landlord, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'email' => ['required', 'email', Rule::unique('landlords')->ignore($this->landlord, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'registration_date'     => 'nullable',
                        'nationality'           => 'nullable',
                        'id_number'             => 'nullable',
                        'state'                 => 'nullable',
                        'city'                  => 'nullable',
                        'postal_address'        => 'nullable',
                        'physical_address'      => 'nullable',
                        'residential_address'   => 'nullable',

                        'confirmed'             => 'nullable',
                        'confirmation_code'     => 'nullable',

                        'password'              => 'nullable|min:3|confirmed',
                        'password_confirmation' => 'required_with:password',

                        'created_by'            => 'nullable',
                        'updated_by'            => 'nullable',
                        'deleted_by'            => 'nullable'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
