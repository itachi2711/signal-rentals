@extends('reports.layouts.master')
@section('title', 'Loan Amortization Schedule')
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
                    Loan Amortization Schedule
                </th>
            </tr>
        </thead>
        <tr>
            <td align="left" class="cell-title-medium cell-text-left">
                Loan #:  {{$loan->loan_reference_number }}
            </td>
            <td align="center" class="cell-title-large cell-text-left">
                Member:  {{$loan->member->first_name}} {{$loan->member->middle_name}} {{$loan->member->last_name}}
            </td>
            <td class="cell-title-medium cell-text-left">
                Phone: {{$loan->member->phone}}
            </td>
        </tr>
        <tr>
            <td align="center" class="cell-title-medium cell-text-left">
                Type: {{$loan->loanType->name}}
            </td>
            <td class="cell-title-large cell-text-left">
                Amount:  {{formatMoney($loan->amount_approved)}}
            </td>
            <td class="cell-title-medium cell-text-left">
                Start Date: {{ formatDate($loan->start_date)}}
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
        @foreach($loan->amortization as $row)
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