<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:16 PM
 */

namespace App\Http\Requests;

class LeaseSettingRequest extends BaseRequest
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
                    'agent_id'                          => 'nullable|exists:agents,id',
                    'lease_number_prefix'               => 'nullable',
                    'invoice_number_prefix'             => 'nullable',
                    'invoice_footer'                    => 'nullable',
                    'invoice_terms'                     => 'nullable',
                    'show_payment_method_on_invoice'    => 'nullable',
                    'generate_invoice_on'               => 'nullable',
                    'next_period_billing'               => 'nullable',
                    'skip_starting_period'              => 'nullable',
                    'waive_penalty'                     => 'nullable'
                ];
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                        'agent_id'                          => 'nullable|exists:agents,id',
                        'lease_number_prefix'               => 'nullable',
                        'invoice_number_prefix'             => 'nullable',
                        'invoice_footer'                    => 'nullable',
                        'invoice_terms'                     => 'nullable',
                        'show_payment_method_on_invoice'    => 'nullable',
                        'generate_invoice_on'               => 'nullable',
                        'next_period_billing'               => 'nullable',
                        'skip_starting_period'              => 'nullable',
                        'waive_penalty'                     => 'nullable'
                    ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
