<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/15/2021
 * Time: 4:42 PM
 */

namespace App\Http\Requests;

class TaskCategoryRequest extends BaseRequest
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
            default:
                break;
        }

        return $rules;

    }
}
