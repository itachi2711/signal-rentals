<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/29/2020
 * Time: 12:11 PM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id'                => $this->id,
            'agent_id'         => $this->agent_id,
            'property_id'         => $this->property_id,
            'lease_id'         => $this->lease_id,
            'account_type'         => $this->account_type,
            'accountBalance'    => $this->accountBalance,

            'account_number'    => $this->account_number,
            'account_code'      => $this->account_code,

            'account_name'      => $this->account_name,
          //  'account_namexxxx'      => $this->account_name,

          //  'extra_charge'        => $this->extra_charge,
            'member'        => $this->member,
            'loan'          => $this->loan,
          //  'account_type_id'   => $this->account_type_id,
           // 'accountType'   => AccountTypeResource::make($this->accountType),
          //  'account_status_id' => $this->account_status_id,
            'other_details'     => $this->other_details,
            'status'            => $this->status,

            'statement' => $this->when(isset($this->statement), function () {
                return $this->transformStatement($this->statement);
            }),

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,

        ];

        if(isset($this->member)){
            $data['account_display_name'] = $this->member->first_name.' '.$this->member->last_name;
        }
        elseif(isset($this->extra_charge)){
            $data['account_display_name'] = $this->extra_charge->charge_name;
        }
        elseif(isset($this->loan)){
            $data['account_display_name'] = 'Loan# '.$this->loan->loan_reference_number;
        }else {
            $data['account_display_name'] = $this->account_name;
        }

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function transformStatement($data) {
        return $data->map(function($item) {
            return [
                'account_id'    => $item->account_id,
                'journal_id'    => $item->journal_id,
                'reference_id'    => $item->reference_id,
                'invoice_number'    => $item->invoice_number,
                'created_at'    => format_date($item->created_at),
                'amount'        => $item->amount,
                'display_amount' => $this->displayAmount($item->amount),
                'is_cr'         => $this->isCr($item->amount),
                'is_dr'         => $this->isDr($item->amount),
                'narration'     => $item->narration,
                'balance'       => format_money($item->balance)
            ];
        })->toArray();
    }

    /**
     * @param $amount
     * @return bool
     */
    private function isCr($amount) {
        return $amount < 0 ? true : false;
    }

    /**
     * @param $amount
     * @return bool
     */
    private function isDr($amount) {
        return $amount > 0 ? true : false;
    }

    /**
     * @param $amount
     * @return float|int
     */
    private function displayAmount($amount) {
        return $this->isCr($amount) ? format_money($amount*-1) : format_money($amount);
    }
}

