<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/30/2021
 * Time: 5:06 AM
 */

namespace App\Http\Requests;

class WaiverRequest extends BaseRequest
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
                    'invoice_id'    => 'required',
                    'property_id'   => 'required',
                    'lease_id'      => 'required',
                    'amount'        => 'required|numeric|min:0|not_in:0',
                    'lease_number'  => 'required',

                    'created_by'    => '',
                    'updated_by'    => '',
                    'deleted_by'    => ''
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'invoice_id'=> '',
                    'property_id'=> '',
                    'lease_id'=> '',
                    'amount'        => 'numeric|min:0|not_in:0',
                    'lease_number'=> '',

                    'created_by'=> '',
                    'updated_by'=> '',
                    'deleted_by'=> ''
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
