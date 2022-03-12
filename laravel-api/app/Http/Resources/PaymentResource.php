<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,

            'agent_id'              => $this->agent_id,
            'payment_method_id'     => $this->payment_method_id,
            'payment_method'        => PaymentMethodResource::make($this->payment_method),
            'currency_id'           => $this->currency_id,
            'tenant_id'             => $this->tenant_id,
            'tenant'                => $this->tenant,
            'lease_id'              => $this->lease_id,
            'lease'                 => LeaseResource::make($this->lease),
            'payment_date'          => format_date($this->payment_date),
            'amount'                => format_money($this->amount),
            'notes'                 => $this->notes,
            'attachment'            => $this->attachment,
            'receipt_number'        => $this->receipt_number,
            'paid_by'               => $this->paid_by,
            'reference_number'      => $this->reference_number,
            'transactions'          => TransactionResource::collection($this->transactions),
            'property_id'           => $this->property_id,
            'property'              => $this->property,
            'lease_number'          => $this->lease_number,

            'payment_status'        => $this->payment_status,
            'approve_user'          => $this->approve_user,
            'cancel_user'           => $this->cancel_user,

			'is_pending' => $this->when(isset($this->payment_status), function () {
                return (boolean) $this->isPending($this->payment_status);
            }),

			'is_cancelled' => $this->when(isset($this->payment_status), function () {
                return (boolean) $this->isCancelled($this->payment_status);
            }),

			'is_approved' => $this->when(isset($this->payment_status), function () {
                return (boolean) $this->isApproved($this->payment_status);
            }),

            'status' => $this->when(isset($this->payment_status), function () {
                return $this->status($this->payment_status);
            }),

            'cancel_notes'      => $this->cancel_notes,
            'cancelled_by'      => $this->cancelled_by,
            'approved_by'       => $this->approved_by,

            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at
        ];
    }

    /**
     * @param $status
     * @return string|string[]
     */
    private function status($status) {
        switch ($status){
            case 'approved' : {
                return [
                    'status_text'   => 'approved',
                    'status_class'  => 'text-success',
                    'status_icon'   => 'done',
                ];
                break;
            }
            case 'pending' : {
                return [
                    'status_text'   => 'pending',
                    'status_class'  => 'text-info',
                    'status_icon'   => 'access_time',
                ];
                break;
            }
            case 'cancelled' : {
                return [
                    'status_text'   => 'cancelled',
                    'status_class'  => 'text-danger',
                    'status_icon'   => 'clear',
                ];
                break;
            }
            default : {
                return '';
                break;
            }
        }
    }

	/**
     * @param $status
     * @return bool
     */
    private function isPending($status) {
        return $status == 'pending' ? true : false;
    }

	/**
     * @param $status
     * @return bool
     */
    private function isCancelled($status) {
        return $status == 'cancelled' ? true : false;
    }

	/**
     * @param $status
     * @return bool
     */
    private function isApproved($status) {
        return $status == 'approved' ? true : false;
    }
}
