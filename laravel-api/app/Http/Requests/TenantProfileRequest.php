<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/20/2021
 * Time: 2:13 PM
 */

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantProfileRequest extends BaseRequest
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
                    'first_name' => 'required',
                    'middle_name' => '',
                    'last_name' => 'required',
                    'user_photo' => '',
                    'photo' => '',
                    'postal_code' => '',
                    'postal_address' => '',
                    'physical_address' => '',
                    'city' => '',
                    'country' => '',
                    'phone' => 'nullable|unique:users,email,NULL,id,deleted_at,NULL',
                    'email' => 'email|nullable|unique:users,email,NULL,id,deleted_at,NULL',
                    'password' => 'required|min:3|confirmed',
                    'password_confirmation' => 'required_with:password'
                ];
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'first_name'        => 'required',
                    'middle_name'       => '',
                    'last_name'         => '',
                    'photo'             => '',
                    'postal_code'       => '',
                    'postal_address'    => '',
                    'physical_address'  => '',
                    'city'              => '',
                    'country'           => '',
                    'phone' => ['nullable', 'regex:/^([0-9\s\-\+\(\)]*)$/',
                        Rule::unique('tenants')->ignore($this->tenant_profile, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                    'email' => ['required', Rule::unique('tenants')->ignore($this->tenant_profile, 'id')
                        ->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })],
                    'current_password' => ['nullable', 'required_with:password', function ($attribute, $value, $fail) {
                        if (!Hash::check($value, Auth::user()->password)) {
                            return $fail(__('The current password is incorrect.'));
                        }
                    }],
                    'password'              => 'nullable|min:3|confirmed',
                    'password_confirmation' => 'required_with:password'
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
