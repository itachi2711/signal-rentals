<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 23/05/2020
 * Time: 19:58
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isNull;

class LeaseResource extends JsonResource
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
            'agent'                 => $this->agent,
            'property_id'           => $this->property_id,
          //  'property'              => PropertyResource::make($this->whenLoaded('property')),
            'property'              => collect($this->property)
                ->intersectByKeys([
                    'id'            => '',
                    'property_code' => '',
                    'property_name' => ''
                ]),
         //   'units'                 => UnitResource::collection($this->whenLoaded('units')),
            'unit_names'            => $this->units->implode('unit_name', ','),
            'units'                 => $this->units,
            'lease_number'          => $this->lease_number,
            'lease_type_id'         => $this->lease_type_id,
            'lease_type'            => collect($this->lease_type)
                ->intersectByKeys(['lease_type_display_name' => '', 'id' => '']),
            'lease_mode_id'         => $this->lease_mode_id,
            'lease_mode'            => collect($this->lease_mode)
                ->intersectByKeys(['lease_mode_display_name' => '', 'id' => '']),
            'start_date'            => format_date($this->start_date),
            'end_date'              => format_date($this->end_date),
            'due_date'              => format_date($this->due_date),
            'rent_amount'           => $this->rent_amount,
            'rent_deposit'          => $this->rent_deposit,
            'billing_frequency'     => $this->billing_frequency,
            'next_billing_date'     => $this->next_billing_date,
            'billed_on'             => format_date($this->billed_on),
            'terminated_on'         => format_date($this->terminated_on),
            'terminated_by'         => $this->terminated_by,
            'status'                => $this->status($this->terminated_on),
            'due_on'                => (int) $this->due_on,
            'waive_penalty'         => (boolean) $this->waive_penalty,
            'skip_starting_period'  => (boolean) $this->skip_starting_period,

            'generate_invoice_on'   => $this->generate_invoice_on,
            'next_period_billing'   => $this->next_period_billing,
          //  'payment_methods_XX'       => PaymentMethodResource::collection($this->whenLoaded('payment_methods')),

			 'payment_methods'       => $this->whenLoaded('payment_methods'),

            'invoice_number_prefix'             => $this->invoice_number_prefix,
            'invoice_footer'                    => $this->invoice_footer,
            'invoice_terms'                     => $this->invoice_terms,
            'show_payment_method_on_invoice'    => $this->show_payment_method_on_invoice,

            'agreement_doc'         => $this->agreement_doc,
            'utilities'             => $this->utilities,
        //    'tenants'               => TenantResource::collection($this->whenLoaded('tenants')),
            'tenants'               => TenantResource::collection($this->tenants),
			'tenant_names'            => $this->tenants->implode('first_name', ','),
            'utility_deposits'      => $this->whenLoaded('utility_deposits'),
            'utility_charges'       => $this->whenLoaded('utility_charges'),
            'extra_charges'         => $this->whenLoaded('extra_charges'),
            'late_fees'             => $this->whenLoaded('late_fees'),
            'terminate_user'             => $this->whenLoaded('terminate_user'),
            'created_by'            => $this->created_by,
            'updated_by'            => $this->updated_by,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }

    /**
     * @param $terminatedOn
     * @return string[]
     */
    private function status($terminatedOn)
    {
        if (is_null($terminatedOn))
            return  [
                'status_text'   => 'Active',
                'status_icon'   => 'done_all',
                'status_color'  => 'text-success',
                'status_btn'    => 'btn-outline-success'
            ];
        else return  [
            'status_text'   => 'Terminated',
            'status_icon'   => 'dangerous',
            'status_color'  => 'text-danger',
            'status_btn'    => 'btn-outline-danger'
        ];
    }
}
