<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/6/2021
 * Time: 7:28 AM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'company_name'                  => $this->company_name,
            'company_type'                  => $this->company_type,
            'email'                         => $this->email,
            'phone'                         => $this->phone,
            'physical_address'              => $this->physical_address,
            'postal_address'                => $this->postal_address,
            'website_url'                	=> $this->website_url,
            'postal_code'                   => $this->postal_code,
            'logo'                          => $this->logo,

            'currency'                      => $this->currency,
            'favicon'                       => $this->favicon,

            'date_format'                   => $this->date_format,
            'amount_thousand_separator'     => $this->amount_thousand_separator,
            'amount_decimal_separator'      => $this->amount_decimal_separator,
            'amount_decimal'                => (int)$this->amount_decimal,

            // Fields with select drop down data
            'date_formats'                  => $this->date_formats,
            'amount_thousand_separators'    => $this->amount_thousand_separators,
            'amount_decimal_separators'     => $this->amount_decimal_separators,
            'amount_decimals'               => $this->amount_decimals,

            'theme'                         => $this->theme,
            'language'                      => $this->language,

            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at
        ];
    }
}
