@extends('reports.layouts.master')
@section('title', 'Calculator Results - Amortization Schedule')
@section('footer')
    @include('reports.layouts.footer', ['setting' => $setting])
@endsection
@section('header')
    @include('reports.layouts.header', ['setting' => $setting])
@endsection
@section('title-content')
    <table  width="100%">
        <thead>
        <tr>
            <th colspan="3" align="left" class="cell-title-medium cell-text-center">
                Calculator - Amortization Schedule
            </th>
        </tr>
        </thead>
        <tr>
            <td class="cell-title-medium cell-text-left">
                Start Date: {{ formatDate($data['start_date'])}}
            </td>
            <td align="center" class="cell-title-medium cell-text-left">
                Loan Type: {{$data['loan_type']}}
            </td>
            <td class="cell-title-medium cell-text-left">
                Interest Type: {{ $data['interest_type_display']}}
            </td>
        </tr>
        <tr>
            <td align="center" class="cell-title-medium cell-text-left">
                Repayment Period: {{$data['period']}}
            </td>
            <td class="cell-title-large cell-text-left">
                Interest Rate:  {{$data['rate']}}
            </td>
            <td class="cell-title-medium cell-text-left">
                Payment Frequency: {{ $data['frequency_display']}}
            </td>
        </tr>
        <tr>
            <td align="center" class="cell-title-medium cell-text-left">
                Amount: {{formatMoney($data['amount'])}}
            </td>
        </tr>
    </table>
@endsection

@section('main-content')
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Period</th>
            <th>Due Date</th>
            <th class="cell-text-right">Payment</th>
            <th class="cell-text-right">Interest</th>
            <th class="cell-text-right">Principal</th>
            <th class="cell-text-right">Balance</th>
        </tr>
        </thead>
        <tbody>
        @foreach($amortization as $row)
            @if($loop->last)
                <tr>
                    <th>{{ $row->count }}</th>
                    <th>{{ $row->due_date }}</th>
                    <th class="cell-text-right">{{ $row->payment }}</th>
                    <th class="cell-text-right">{{ $row->interest }}</th>
                    <th class="cell-text-right">{{ $row->principal }}</th>
                    <th class="cell-text-right">{{ $row->balance }}</th>
                </tr>
            @else
                <tr>
                    <td>{{ $row->count }}</td>
                    <td>{{ $row->due_date }}</td>
                    <td class="cell-text-right">{{ $row->payment }}</td>
                    <td class="cell-text-right">{{ $row->interest }}</td>
                    <td class="cell-text-right">{{ $row->principal }}</td>
                    <td class="cell-text-right">{{ $row->balance }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@endsection