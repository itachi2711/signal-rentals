<?php


namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ExtraChargeRequest extends BaseRequest
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
                    'extra_charge_name'             => 'required|unique:extra_charges,extra_charge_name,NULL,id,deleted_at,NULL',
                    'extra_charge_display_name'     => 'required|unique:extra_charges,extra_charge_display_name,NULL,id,deleted_at,NULL',
                    'extra_charge_description'      => ''
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules = [
                    'extra_charge_name' => ['required', Rule::unique('extra_charges')->ignore($this->extra_charge, 'id')
                        ->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })],

                    'extra_charge_display_name' => ['required', Rule::unique('extra_charges')->ignore($this->extra_charge, 'id')
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
