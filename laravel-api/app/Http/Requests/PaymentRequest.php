<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentRequest extends BaseRequest
{
      /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        switch($this->method())
        {
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
                        'payment_method_id' => 'required',
                        'currency_id'       => '',
                        'tenant_id'         => 'required|exists:tenants,id',
                        'lease_id'          => 'required|exists:leases,id',
                        'lease_number'      => 'required',
                        'property_id'       => 'exists:properties,id',
                        'payment_date'      => 'required',
                        'amount'            => 'required|numeric|min:0|not_in:0',
                        'notes'             => '',
                        'paid_by'           => '',
                        'reference_number'  => ''
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
            default:break;
        }
        return $rules;
    }
}
