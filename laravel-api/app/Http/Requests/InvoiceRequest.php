<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/28/2020
 * Time: 8:38 AM
 */

namespace App\Http\Requests;

class InvoiceRequest extends BaseRequest
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
                    'agent_id' => 'exists:agents,id',
                    'property_id' => ''

                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'status' => '',
                    'updated_on' => ''
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
