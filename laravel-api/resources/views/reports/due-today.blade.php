@extends('reports.layouts.master')
@section('title', 'Loans Due Today')
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
                    Loans Due Today : {{$today}}
                </th>
            </tr>
        </thead>
    </table>
@endsection

@section('main-content')
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Officer</th>
            <th>Loan #.</th>
            <th>Loan Type</th>
            <th>Member</th>
            <th>Phone</th>
            <th class="cell-text-right">Principal</th>
            <th class="cell-text-right">Interest</th>
            <th class="cell-text-right">Penalty</th>
            <th class="cell-text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($loans['data'] as $key => $value)
            <tr>
                <td>{{ Illuminate\Support\Str::limit($value->loan_officer_first_name, 12) }}</td>
                <td>{{ $value->loan_reference_number }}</td>
                <td>{{ Illuminate\Support\Str::limit($value->loan_type_name, 12)}}</td>
                <td>{{ Illuminate\Support\Str::limit($value->member_first_name, 12) }}</td>
                <td>{{ Illuminate\Support\Str::limit($value->member_phone, 13) }}</td>
                <td class="cell-text-right">{{ $value->pendingPrincipal }}</td>
                <td class="cell-text-right">{{ $value->pendingInterest }}</td>
                <td class="cell-text-right">{{ $value->pendingPenalty }}</td>
                <td class="cell-text-right">{{ $value->totalDue }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection