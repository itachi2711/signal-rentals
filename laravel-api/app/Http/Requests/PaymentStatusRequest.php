<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/6/2021
 * Time: 8:37 PM
 */

namespace App\Http\Requests;

class PaymentStatusRequest extends BaseRequest
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
                    'id' => 'exists:payments,id',
                    'payment_status' => '',
                    'status_comments' => ''
                ];
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'payment_id' => 'exists:payments,id',
                    'payment_status' => '',
                    'status_comments' => ''
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
