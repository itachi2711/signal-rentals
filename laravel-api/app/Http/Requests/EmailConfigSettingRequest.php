<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:16 PM
 */

namespace App\Http\Requests;

class EmailConfigSettingRequest extends BaseRequest
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
                    'protocol' => '',
                    'smpt_host' => '',
                    'smpt_username' => '',
                    'smpt_password' => '',
                    'smpt_port' => '',
                    'mail_gun_domain' => '',
                    'mail_gun_secret' => '',
                    'mandrill_secret' => '',
                    'from_name' => '',
                    'from_email' => ''
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'protocol' => '',
                    'smpt_host' => '',
                    'smpt_username' => '',
                    'smpt_password' => '',
                    'smpt_port' => '',
                    'mail_gun_domain' => '',
                    'mail_gun_secret' => '',
                    'mandrill_secret' => '',
                    'from_name' => '',
                    'from_email' => ''
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
