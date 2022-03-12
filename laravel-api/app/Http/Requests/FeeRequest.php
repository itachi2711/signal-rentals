<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 15/05/2020
 * Time: 23:14
 */

namespace App\Http\Requests;

class FeeRequest extends BaseRequest
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
                        'property_id' => '',
                        'name' => '',
                        'description' => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'agent_id' => 'exists:agents,id',
                        'property_id' => '',
                        'name' => '',
                        'description' => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
