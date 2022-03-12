<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:13 PM
 */

namespace App\Http\Requests;

class TenantSettingRequest extends BaseRequest
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
                    'agent_id'              => 'nullable|exists:agents,id',
                    'tenant_number_prefix'  => 'nullable',
                    'next_tenant_number'    => 'nullable'
                ];
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'agent_id'              => 'nullable|exists:agents,id',
                    'tenant_number_prefix'  => 'nullable',
                    'next_tenant_number'    => 'nullable'
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
