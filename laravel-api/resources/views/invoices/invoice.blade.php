<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>INVOICE: {{$invoice['invoice_number']}}</title>
    @include('styles.bootstrap-styles')
    @include('invoices.styles.invoice-style')
</head>
<body>
<div id="container">
    @include('invoices.layouts.header', array('setting' => $settings))
    <table>
        <tr>
            @foreach($invoice['lease']['tenants'] as $tenant)
                @if($loop->index == 0)
                    <td class="bg-white text-left mr-2" style="width: 35%;">
                        <div class="to">INVOICE TO:</div>
                        <h2 class="name"> {{$tenant->first_name}}  {{$tenant->last_name}}</h2>
                        <div class="address">{{$tenant->physical_address}}</div>
                        <div class="email"><a href="mailto:{{$tenant->email}}">{{$tenant->email}}</a></div>
                    </td>
                @endif
            @endforeach

            <td class="text-center ml-5" style="width: 30%;">
                <button type="submit" class="btn {{$invoice['summary']['status']['status_btn']}}">
                    {{$invoice['summary']['status']['status_text']}}
                </button>
            </td>

            <td class="bg-white" style="width: 35%">
                <h4 class="to">INVOICE: {{$invoice['invoice_number']}}</h4>
                <div class="date">Date of Invoice: {{$invoice['invoice_date']}}</div>
                <div class="date">Due Date:  {{$invoice['due_date']}}</div>
            </td>
        </tr>
    </table>
         <section id="items">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>ITEM NAME</th>
                    <th>UNIT PRICE</th>
                    <th>QUANTITY</th>
                    <th>TOTAL</th>
                </tr>
                </thead>
                <tbody>
                @foreach($invoice['invoice_items'] as $row)
                    <tr>
                        <td>{{ $loop->index + 1 }}.</td>
                        <td>
                            <blockquote class="blockquote" style="padding-bottom: 0">
                                <p class="mb-0">{{$row->item_name}}</p>
                                <p class="blockquote-footer">{{$row->item_description}}</p>
                            </blockquote>
                        </td>
                        <td>{{format_money($row->price)}}</td>
                        <td>{{$row->quantity}}</td>
                        <td>{{format_money($row->amount)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
        <table>
            <tr>
                <td style="width: 55%; height: 150px; vertical-align:bottom; text-align:left; position: absolute">
                </td>
                <td style="width: 45%;" id="sums">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <th>Sub Total</th>
                                <td>{{ $invoice['summary']['invoice_amount'] }}</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td>{{ $invoice['summary']['invoice_amount'] }}</td>
                            </tr>
                            <tr data-hide-on-quote="true">
                                <th>Amount Paid</th>
                                <td>{{ $invoice['summary']['amount_paid'] }}</td>
                            </tr>
                            <tr  class="amount-total">
                                <th>Amount Due</th>
                                <td>{{ $invoice['summary']['amount_due'] }}</td>
                            </tr>
                        </table>
                </td>
            </tr>
        </table>
    <footer>
        {{--{{$invoice['invoice_footer']}}--}}
    </footer>
</div>
</body>
</html>
