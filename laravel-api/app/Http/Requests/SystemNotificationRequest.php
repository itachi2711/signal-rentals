<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/10/2021
 * Time: 9:31 AM
 */

namespace App\Http\Requests;

class SystemNotificationRequest extends BaseRequest
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
                    'name'          => 'nullable',
                    'display_name'  => 'nullable',
                    'send_email'    => 'nullable',
                    'send_sms'      => 'nullable'
                ];
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'name'          => 'nullable',
                    'display_name'  => 'nullable',
                    'send_email'    => 'nullable',
                    'send_sms'      => 'nullable'
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
