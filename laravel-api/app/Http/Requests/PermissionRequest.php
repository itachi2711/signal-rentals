<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/6/2021
 * Time: 7:39 AM
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PermissionRequest extends BaseRequest
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
                    'name'          => 'required|unique:permissions,name,NULL,id,deleted_at,NULL',
                    'display_name'  => 'required|unique:permissions,display_name,NULL,id,deleted_at,NULL',
                    'description'   => ''
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'name' => ['required', 'exists:permissions', Rule::unique('permissions')->ignore($this->permission, 'id')
                        ->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })],

                    'display_name' => ['required', Rule::unique('permissions')->ignore($this->permission, 'id')
                        ->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })],
                    'description'   => 'nullable'
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
