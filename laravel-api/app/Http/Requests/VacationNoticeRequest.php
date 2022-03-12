<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/15/2021
 * Time: 4:51 PM
 */

namespace App\Http\Requests;

class VacationNoticeRequest extends BaseRequest
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
                    'agent_id'          => '',
                    'tenant_id'         => '',
                    'lease_id'          => '',
                    'property_id'       => '',
                    'unit'              => '',
                    'date_received'     => '',
                    'vacating_date'     => '',
                    'vacating_reason'   => ''
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
