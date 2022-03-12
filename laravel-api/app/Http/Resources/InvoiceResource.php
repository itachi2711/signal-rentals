<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/28/2020
 * Time: 8:37 AM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceResource extends JsonResource
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
           // 'property'              => PropertyResource::make($this->whenLoaded('property')),
           /* 'property'              => collect($this->property)
                ->intersectByKeys([
                    'id'            => '',
                    'property_code' => '',
                    'property_name' => '',
                    ]),*/
           // 'period'                => PeriodResource::make($this->whenLoaded('period')),
          //  'period'                => PeriodResource::make($this->period),
            'period'              => collect($this->period)
                ->intersectByKeys([
                    'id'    => '',
                    'name'  => ''
                ]),
            'period_name'              => $this->period_name,
            // 'invoice_items'         => InvoiceItemResource::collection($this->whenLoaded('invoice_items')),
            'invoice_items'         => InvoiceItemResource::collection($this->invoice_items),
            'summary'               => $this->invoiceSummary(
                $this->invoice_items, $this->amount_paid, $this->due_date, $this->paid_on
            ),
            'payment_transactions'  => TransactionResource::collection($this->payment_transactions),
            'payment_summary'       => $this->paymentSummary($this->payment_transactions),
            'waiver_summary'       => $this->waiverSummary($this->waiver_transactions),
            'property_id'           => $this->property_id,
            'period_id'             => $this->period_id,
            'lease_id'              => $this->lease_id,
            'lease'                 => LeaseResource::make($this->lease),
          /*  'lease'                 => collect(LeaseResource::make($this->lease))
                ->intersectByKeys([
                    'id'            => '',
                    'lease_number'  => '',
                    'property'      => '',
                    'tenants'      => '',
                    'unit_names'    => '',
                ]),*/
            'late_fee_charged_on'   => $this->late_fee_charged_on,
            'amount_paid'           => $this->amount_paid,
            'currency'              => $this->currency,
            'invoice_number'        => $this->invoice_number,
            'invoice_date'          => format_date($this->invoice_date),
            'due_date'              => format_date($this->due_date),
            'paid_on'               => $this->paid_on,
            'terms'                 => $this->terms,
            'notes'                 => $this->notes,
            'total_items'           => $this->total_items,
            'sub_total'             => $this->sub_total,
            'total_tax'             => $this->total_tax,
            'total_discount'        => $this->total_discount,
            'invoice_amount'        => $this->invoice_amount,
            'created_by'            => $this->created_by,
            'updated_by'            => $this->updated_by,

            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }

    /**
     * @param $paidDate
     * @param $dueDate
     * @param $amountPaid
     * @param $invoiceAmount
     * @return string[]
     */
    private function invoiceStatus($paidDate, $dueDate, $amountPaid, $invoiceAmount)
    {
        $paidOn = Carbon::make($paidDate);
        $dueDate = Carbon::make($dueDate);
        $today = Carbon::make(date('Y-m-d'));

        if ($paidOn != null) {
            return  [
                'status_text'   => 'Paid',
                'status_icon'   => 'done_all',
                'status_color'  => 'text-success',
                'status_btn'    => 'btn-outline-success'
            ];
        }
        if ($dueDate->equalTo($today)) {
            return  [
                'status_text'   => 'Due Today',
                'status_icon'   => 'info',
                'status_color'  => 'text-warning',
                'status_btn'    => 'btn-outline-warning'
            ];
        }
        if (isset($dueDate) && $dueDate->lessThan($today)) {
            return  [
                'status_text'   => 'Over Due',
                'status_icon'   => 'dangerous',
                'status_color'  => 'text-danger',
                'status_btn'    => 'btn-outline-danger'
            ];
        }
        if ($amountPaid == 0) {
            return  [
                'status_text'   => 'UnPaid',
                'status_icon'   => 'cancel',
                'status_color'  => 'text-warning',
                'status_btn'    => 'btn-outline-warning'
            ];
        }
        if ($amountPaid < $invoiceAmount) {
            return  [
                'status_text'   => 'Partially Paid',
                'status_icon'   => 'done',
                'status_color'  => 'text-info',
                'status_btn'    => 'btn-outline-info'
            ];
        }
        return  [
            'status_text'   => '',
            'status_icon'   => '',
            'status_color'  => '',
            'status_btn'    => ''
        ];
    }

    /**
     * @param $invoiceItems
     * @param $paid
     * @param $dueDate
     * @param $paidOn
     * @return array
     */
    private function invoiceSummary($invoiceItems, $paid, $dueDate, $paidOn) {
        if(count($invoiceItems) == 0) {
            return [];
        }
        $count = 0;
        $invoiceAmount = 0;
        $summary = [];
        foreach ($invoiceItems as $key => $value) {
            $count++;
            $invoiceAmount = $invoiceAmount + $value['price'];

            $summary['count'] = $count;
            $summary['invoice_amount']  = $invoiceAmount;
            $summary['amount_paid']     = $paid;
            $summary['amount_due']      = $invoiceAmount - $paid;
        }
        $status = $this->invoiceStatus($paidOn, $dueDate, $paid, $invoiceAmount);

        $summary['invoice_amount_number']  = $summary['invoice_amount'];
        $summary['invoice_amount']  = format_money($summary['invoice_amount']);
        $summary['amount_paid_number'] = $summary['amount_paid'];
        $summary['amount_paid'] = format_money($summary['amount_paid']);
        $summary['amount_due_number'] = $summary['amount_due'];
        $summary['amount_due'] = format_money($summary['amount_due']);
        $summary['status'] = $status;
        return $summary;
    }

    /**
     * @param $waiverTransactions
     * @return array
     */
    private function waiverSummary($waiverTransactions) {
        $count = 0;
        $totalAmount = 0;
        $summary = [];
        foreach ($waiverTransactions as $key => $value) {
            $count++;
            $totalAmount = $totalAmount + $value['transaction_amount'];
            if($value['transaction_type'] == WAIVER) {
                $summary['transactions_count']  = $count;
                $summary['transactions_total']  = format_money($totalAmount);
            }
        }
        return $summary;
    }

    /**
     * @param $paymentTransactions
     * @return array
     */
    private function paymentSummary($paymentTransactions) {
        $count = 0;
        $totalAmount = 0;
        $summary = [];
        $items = [];
        foreach ($paymentTransactions as $key => $value) {
            $count++;
            $totalAmount = $totalAmount + $value['transaction_amount'];

            if($value['transaction_type'] == PAYMENT) {
                $x = new \stdClass();
                $x->item_name = $value['invoice_item']['item_name'];
                $x->amount_paid = format_money($value['transaction_amount']);
                $x->payment_date = $value['payment']['payment_date'];
                $x->payment_date_ago = Carbon::parse($value['payment']['payment_date'])->diffForHumans();
                $x->receipt_number = $value['payment']['receipt_number'];
                $x->payment_reference = $value['payment']['reference_number'];
                $x->receipt_amount = $value['payment']['amount'];
                $x->payment_method_name = $value['payment']['payment_method']['payment_method_display_name'];
                $items[] = $x;
                $summary['transactions_count']  = $count;
                $summary['transactions_total']  = format_money($totalAmount);
            }
        }
        $summary['items'] = $items;

        $receiptItems = [];
	   foreach ($items as $item) {
		   $receiptItems[$item->receipt_number]['items'][] = $item;

		   $receiptItems[$item->receipt_number]['summary']['receipt_number'] = $item->receipt_number;
		   $receiptItems[$item->receipt_number]['summary']['amount'] = format_money($item->receipt_amount);
		   $receiptItems[$item->receipt_number]['summary']['payment_reference'] = $item->payment_reference;
		   $receiptItems[$item->receipt_number]['summary']['payment_date_ago'] = $item->payment_date_ago;
		   $receiptItems[$item->receipt_number]['summary']['payment_date'] = format_date($item->payment_date);
		   $receiptItems[$item->receipt_number]['summary']['payment_method_name'] = $item->payment_method_name;
		  }
		$summary['receipt_items'] = $receiptItems;

        return $summary;
    }
}
