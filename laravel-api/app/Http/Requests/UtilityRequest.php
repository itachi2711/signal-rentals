<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 15/05/2020
 * Time: 23:01
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UtilityRequest extends BaseRequest
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
                        'utility_name'          => 'required|unique:utilities,utility_name,NULL,id,deleted_at,NULL',
                        'utility_display_name'  => 'required|unique:utilities,utility_display_name,NULL,id,deleted_at,NULL',
                        'utility_description'   => 'nullable'
                    ];
                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'utility_name' => ['required', Rule::unique('utilities')->ignore($this->utility, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'utility_display_name' => ['required', Rule::unique('utilities')->ignore($this->utility, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'utility_description'   => 'nullable'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
