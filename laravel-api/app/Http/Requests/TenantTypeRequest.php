<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 22/05/2020
 * Time: 13:06
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class TenantTypeRequest extends BaseRequest
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
                        'tenant_type_name'          => 'required|unique:tenant_types,tenant_type_name,NULL,id,deleted_at,NULL',
                        'tenant_type_display_name'  => 'required|unique:tenant_types,tenant_type_display_name,NULL,id,deleted_at,NULL',
                        'tenant_type_description'   => 'nullable'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'agent_id' => 'exists:agents,id',
                        'tenant_type_name' => ['required', Rule::unique('tenant_types')->ignore($this->tenant_type, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'tenant_type_display_name' => ['required', Rule::unique('tenant_types')->ignore($this->tenant_type, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'tenant_type_description' => 'nullable'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
