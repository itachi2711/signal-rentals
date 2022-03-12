<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 2/3/2021
 * Time: 7:30 AM
 */

namespace App\Http\Requests;

class TaskRequest extends BaseRequest
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
                    'title'=> '',
                    'date'=> '',
                    'lease_id'=> '',
                    'property_id'=> '',
                    'unit_id'=> '',
                    'tenant_id'=> '',
                    'task_category'=> '',
                    'priority'=> '',
                    'status'=> '',
                    'description'=> ''
                ];
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'title'=> '',
                    'date'=> '',
                    'lease_id'=> '',
                    'property_id'=> '',
                    'unit_id'=> '',
                    'tenant_id'=> '',
                    'task_category'=> '',
                    'priority'=> '',
                    'status'=> '',
                    'description'=> ''
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}
