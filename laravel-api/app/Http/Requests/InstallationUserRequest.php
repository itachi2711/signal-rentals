<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 31/12/2019
 * Time: 18:15
 */

namespace App\Http\Requests;

class InstallationUserRequest extends BaseRequest
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
                        'first_name'            => 'required',
                        'middle_name'           => '',
                        'last_name'             => 'required',
                        'email'                 => 'email|required',
                        'password'              => 'required|min:3|confirmed',
                        'password_confirmation' => 'required_with:password'
                    ];
                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'first_name'            => 'required',
                        'middle_name'           => '',
                        'last_name'             => 'required',
                        'email'                 => 'email|required',
                        'password'              => 'required|min:3|confirmed',
                        'password_confirmation' => 'required_with:password'
                    ];
                    break;
                }
            default:
                break;
        }
        return $rules;
    }
}