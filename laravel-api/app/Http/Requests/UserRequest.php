<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                    break;
                }
            case 'POST':
                {
                    $rules = [
                        'first_name'            => 'required',
                        'middle_name'           => '',
                        'last_name'             => 'required',
                        'user_photo'            => '',
                        'photo'                 => '',
                        'postal_code'           => '',
                        'postal_address'        => '',
                        'physical_address'      => '',
                        'city'                  => '',
                        'country'               => '',
                        'role_id'               => 'required|exists:roles,id',
                        'phone'                 => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,phone,NULL,id,deleted_at,NULL',
                        'email'                 => 'email|required|unique:users,email,NULL,id,deleted_at,NULL',
                        'password'              => 'required|min:3|confirmed',
                        'password_confirmation' => 'required_with:password'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'first_name'            => '',
                        'middle_name'           => '',
                        'last_name'             => 'required',
                        'photo'                 => '',
                        'postal_code'           => '',
                        'postal_address'        => '',
                        'physical_address'      => '',
                        'city'                  => '',
                        'country'               => '',
                        'role_id'               => 'required|exists:roles,id',
                        'phone' => ['nullable', 'regex:/^([0-9\s\-\+\(\)]*)$/',
                            Rule::unique('users')->ignore($this->user, 'id')
                                ->where(function ($query) {
                                    $query->where('deleted_at', NULL);
                                })],
                        'password'              => 'nullable|min:3|confirmed',
                        'password_confirmation' => 'required_with:password',

                        'email' => ['required', 'email', Rule::unique('users')->ignore($this->user, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'current_password' => ['nullable', 'required_with:password', function ($attribute, $value, $fail) {
                            if (!Hash::check($value, Auth::user()->password)) {
                                return $fail(__('The current password is incorrect.'));
                            }
                        }],
                    ];
                    break;
                }
            default:break;
        }
        return $rules;
    }
}
