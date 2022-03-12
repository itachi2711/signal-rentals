<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 28/05/2020
 * Time: 21:52
 */

namespace App\Http\Requests;

class UtilityBillRequest extends BaseRequest
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

                        'unit_id' => '',
                        'utility_id' => '',
                        'previous_reading' => '',
                        'current_reading' => '',
                        'reading_date' => '',
                        'units' => '',
                        'base_charge' => '',
                        'rate_per_unit' => '',
                        'total' => '',

                        'created_by' => '',
                        'updated_by' => '',

                        'deleted_by'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'agent_id' => 'exists:agents,id',

                        'unit_id' => '',
                        'utility_id' => '',
                        'previous_reading' => '',
                        'current_reading' => '',
                        'reading_date' => '',
                        'units' => '',
                        'base_charge' => '',
                        'rate_per_unit' => '',
                        'total' => '',

                        'created_by' => '',
                        'updated_by' => '',

                        'deleted_by'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
