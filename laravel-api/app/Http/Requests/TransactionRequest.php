<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/30/2021
 * Time: 5:00 AM
 */

namespace App\Http\Requests;

class TransactionRequest extends BaseRequest
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
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'agent_id' => 'exists:agents,id',
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
