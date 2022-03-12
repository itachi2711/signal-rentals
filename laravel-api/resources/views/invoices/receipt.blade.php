<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>RECEIPT : {{$payment['receipt_number']}}</title>
    @include('styles.bootstrap-styles')
    @include('invoices.styles.invoice-style')
</head>
<body>
<div id="container">
    @include('invoices.layouts.header', array('setting' => $settings))
    <table>
        <tr>
            <td class="bg-white text-left">
                <div class="to">TO:</div>
                <h2 class="name">{{$payment['tenant']['first_name']}} {{$payment['tenant']['last_name']}}</h2>
                <div class="address">{{$payment['tenant']['postal_address']}}</div>
                <div class="email"><a href="{{$payment['tenant']['email']}}">{{$payment['tenant']['email']}}</a></div>
            </td>
            <td class="bg-white">
                <h6 class="to"> Receipt # :  {{$payment['receipt_number']}}</h6>
            </td>
        </tr>
    </table>

    <section id="items">
        <table class="table">
            <thead>
            <tr>
                <th colspan="2" class="text-left">Payment Details</th>
            </tr>
            </thead>
            <tbody class="text-left">
            <tr><td>Amount</td><td>{{ format_money($payment['amount']) }}</td></tr>
            <tr><td>Payment Date</td><td>{{ format_date($payment['payment_date']) }}</td></tr>
            <tr><td>Payment Method</td><td>{{$payment['payment_method']['payment_method_display_name']}}</td></tr>
            <tr><td>Tenant</td><td>{{$payment['tenant']['first_name']}}</td></tr>
            <tr><td>Lease</td><td>{{$payment['lease']['lease_number']}}</td></tr>
            <tr><td>Property</td><td>
                    {{$payment['property']['property_name']}}
                    ({{$payment['property']['property_code']}})
                    - {{$payment['property']['location']}}
                </td></tr>
            <tr><td>Unit</td><td>
                    @foreach($payment['lease']['units'] as $unit)
                        {{$unit['unit_name']}}
                    @endforeach
                </td>
            </tr>
            </tbody>
        </table>
    </section>

    <section id="items">
        <table class="table">
            <thead>
            <tr>
                <th colspan="3" class="text-left">Transaction Details</th>
            </tr>
            </thead>
           <tbody class="text-left">
            @foreach($payment['transactions'] as $row)
                <tr>
                    <td>{{ $loop->index + 1 }}.</td>
                    <td>
                        <blockquote class="blockquote" style="padding-bottom: 0">
                            <p class="mb-0">{{$row->invoice_item['item_name']}}</p>
                        </blockquote>
                    </td>
                    <td class="text-right">{{format_money($row->transaction_amount)}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
    <footer>
       {{-- Receipt was created on a computer and is valid without the signature and seal.--}}
    </footer>
</div>
</body>
</html>
