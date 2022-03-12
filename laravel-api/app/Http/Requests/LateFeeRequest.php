<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/27/2021
 * Time: 9:55 AM
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LateFeeRequest extends BaseRequest
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
                    'late_fee_name'             => 'required|unique:late_fees,late_fee_name,NULL,id,deleted_at,NULL',
                    'late_fee_display_name'     => 'required|unique:late_fees,late_fee_display_name,NULL,id,deleted_at,NULL',
                    'late_fee_description'      => ''
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'late_fee_name' => ['required', Rule::unique('late_fees')->ignore($this->late_fee, 'id')
                        ->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })],

                    'late_fee_display_name' => ['required', Rule::unique('late_fees')->ignore($this->late_fee, 'id')
                        ->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })],
                ];
                break;
            }
            default:
                break;
        }

        return $rules;

    }
}

