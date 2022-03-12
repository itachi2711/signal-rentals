<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 15/05/2020
 * Time: 23:08
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AmenityRequest extends BaseRequest
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
                        'amenity_name'          => 'required|unique:amenities,amenity_name,NULL,id,deleted_at,NULL',
                        'amenity_display_name'  => 'required|unique:amenities,amenity_display_name,NULL,id,deleted_at,NULL',
                        'amenity_description'   => 'nullable'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'amenity_name' => ['required', Rule::unique('amenities')->ignore($this->amenity, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'amenity_display_name' => ['required', Rule::unique('amenities')->ignore($this->amenity, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'amenity_description'   => 'nullable'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
