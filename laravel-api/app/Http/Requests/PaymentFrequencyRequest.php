<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 22/05/2020
 * Time: 17:59
 */

namespace App\Http\Requests;

class PaymentFrequencyRequest extends BaseRequest
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
                        'payment_frequency_name' => '',
                        'payment_frequency_display_name' => '',
                        'payment_frequency_description' => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'agent_id' => 'exists:agents,id',
                        'payment_frequency_name' => '',
                        'payment_frequency_display_name' => '',
                        'payment_frequency_description' => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
