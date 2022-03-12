<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:16 PM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaseSettingResource extends JsonResource
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
            'id'                    => $this->id,

            'agent_id'              => $this->agent_id,
            'lease_number_prefix'   => $this->lease_number_prefix,
            'generate_invoice_on'   => $this->generate_invoice_on,
            'next_period_billing'   => $this->next_period_billing,
            'skip_starting_period'  => $this->skip_starting_period,
            'waive_penalty'         => $this->waive_penalty,

            'invoice_number_prefix'             => $this->invoice_number_prefix,
            'invoice_footer'                    => $this->invoice_footer,
            'invoice_terms'                     => $this->invoice_terms,
            'show_payment_method_on_invoice'    => $this->show_payment_method_on_invoice,

            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
