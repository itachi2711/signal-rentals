<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:05 PM
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CommunicationSettingRequest extends BaseRequest
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
                    'name' => 'required|unique:communication_settings,name,NULL,id,deleted_at,NULL',
                    'display_name' => 'required|unique:communication_settings,display_name,NULL,id,deleted_at,NULL',
                    'email_template' => '',
                    'sms_template' => '',
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'name' => ['required', Rule::unique('communication_settings')->ignore($this->communication_setting, 'id')
                        ->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })],

                    'display_name' => ['required', Rule::unique('communication_settings')->ignore($this->communication_setting, 'id')
                        ->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })],
                    'email_template' => '',
                    'sms_template' => '',
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
