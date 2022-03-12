@extends('reports.layouts.master')
@section('title', 'Account Statement')
@section('footer')
    @include('reports.layouts.footer', ['setting' => $settings])
@endsection
@section('header')
    @include('reports.layouts.header', ['setting' => $settings])
@endsection
@section('title-content')
    <table  width="100%">

        @isset($lease->tenants)
            <tr>
                <td class="text-left">{{$lease->property['property_name']}} ( {{$lease->property['property_code']}} )
                    - {{$lease->property['location']}}</td>
            </tr>
        @endisset
        @isset($lease->units)
            @foreach($lease->units as $unit)
                <tr>
                    <td class="text-left">{{$unit['unit_name']}} </td>
                </tr>
            @endforeach
        @endisset
        @isset($lease->tenants)
            @foreach($lease->tenants as $tenant)
                <tr>
                    <td class="text-left">{{$tenant['first_name']}} {{$tenant['last_name']}}</td>
                </tr>
            @endforeach
        @endisset
            <tr>
                <td class="cell-title-large text-left">
                    {{ Illuminate\Support\Str::limit($pageData['account_display_name'], 50)}}
                    ({{$pageData['account_number']}})
                </td>
            </tr>
    </table>
@endsection

@section('main-content')
    <table class="table table-sm">
        <thead>
            <tr>
                <th class="text-left">Date</th>
                <th class="text-left">Narration</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pageData['statement'] as $row)
            <tr>
                <td class="text-left">{{format_date($row['created_at'])}}</td>
                <td class="text-left">{{$row['narration']}}</td>
                <td class="text-right">{{ $row['is_dr'] ? $row['display_amount'] : '-' }}</td>
                <td class="text-right">{{ $row['is_cr'] ? $row['display_amount'] : '-' }}</td>
                <td class="text-right">{{$row['balance']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
