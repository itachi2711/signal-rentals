<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 22/05/2020
 * Time: 17:52
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentMethodRequest extends BaseRequest
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
                        'payment_method_name'           => 'required|unique:payment_methods,payment_method_name,NULL,id,deleted_at,NULL',
                        'payment_method_display_name'   => 'required|unique:payment_methods,payment_method_display_name,NULL,id,deleted_at,NULL',
                        'payment_method_description'    => 'nullable'
                    ];
                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'agent_id' => 'nullable|exists:agents,id',
                        'payment_method_name' => ['required', Rule::unique('payment_methods')->ignore($this->payment_method, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'payment_method_display_name' => ['required', Rule::unique('payment_methods')->ignore($this->payment_method, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'payment_method_description' => 'nullable'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
