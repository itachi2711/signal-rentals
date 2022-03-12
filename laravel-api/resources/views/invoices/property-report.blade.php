<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{$report->property['property_name']}} - ({{$report->property['property_code']}})
         {{$report->period->name}}</title>
    @include('styles.bootstrap-styles')
    @include('invoices.styles.invoice-style')
</head>
<body>
<div id="container">
    @include('invoices.layouts.header', array('setting' => $settings))
    <table>
        <tr class="text-left">
            <td><h6>{{$report->property['property_name']}} - ({{$report->property['property_code']}}) </h6></td>
        </tr>
        <tr class="text-left">
            <td><h6>{{$report->period->name}}</h6></td>
        </tr>
        <tr class="text-right">
            <td colspan="2">Total Billed:</td> <td>{{$report->total_current_property_billing['total_billed']}}</td>
        </tr>
       <tr class="text-right">
           <td colspan="2">Paid:</td> <td>{{$report->total_current_property_billing['total_paid']}}</td>
       </tr>
        <tr class="text-right">
            <td colspan="2">Pending:</td> <td>{{$report->total_current_property_billing['total_pending']}}</td>
        </tr>
    </table>
    <section id="items">
        <table class="table">
            <tr class="text-left">
                <th>Active Leases</th>
            </tr>
        </table>

        <table class="table">
            <thead>
            <tr class="text-left">
                <td>#</td>
                <td>Lease Number</td>
                <td>Unit</td>
                <td class="text-right">Prev. Balance</td>
                <td class="text-right">Billed</td>
                <td class="text-right">Paid</td>
                <td class="text-right">Pending</td>
            </tr>
            </thead>
            <tbody>
            @foreach($report->leases as $row)
                <tr  class="text-left">
                    <td>{{ $loop->index + 1 }}.</td>
                    <td>{{ $row->lease_number }}</td>
                    <td>
                        @foreach($row['units'] as $unit)
                            {{$unit->unit_name}}
                        @endforeach
                    </td>
                    <td class="text-right">
                        @if($row->previous_billing)
                            {{$row->previous_billing['pending_amount']}}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($row->current_billing)
                            {{$row->current_billing['invoice_amount']}}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($row->current_billing)
                            {{$row->current_billing['amount_paid']}}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($row->current_billing)
                            {{$row->current_billing['pending_amount']}}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <table class="table">
            <tr class="text-left">
                <th>Vacant Units ({{$report->property['total_vacant_units']}})</th>
            </tr>
        </table>
        <table class="table">
            <thead>
            <tr class="text-left">
                <td>#</td>
                <td>Unit Name</td>
                <td>Unit Type</td>
                <td>Mode</td>
            </tr>
            </thead>
            <tbody>
            @foreach($report->property['vacant_units'] as $row)
                <tr  class="text-left">
                    <td>{{ $loop->index + 1 }}.</td>
                    <td>{{$row->unit_name}}</td>
                    <td>{{$row->unit_type->unit_type_display_name}}</td>
                    <td>{{$row->unit_mode}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
    <footer>
        {{--Document is computer generated and is valid without the signature and seal.--}}
    </footer>
</div>
</body>
</html>
