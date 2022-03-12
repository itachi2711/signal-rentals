@extends('reports.layouts.master')
@section('title', 'OverDue Loans')
@section('footer')
    @include('reports.layouts.footer', ['setting' => $setting])
@endsection
@section('header')
    @include('reports.layouts.header', ['setting' => $setting])
@endsection
@section('title-content')
    <table  width="100%" class="table-fixed">
        <thead>
        <tr>
            <th colspan="3" align="left" class="cell-title-medium cell-text-center">
                Loans OVERDUE : {{$today}}
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
            <th>Member</th>
            <th>Phone</th>
            <th class="cell-text-right"><div>Principal</div> (Date Due)</th>
            <th class="cell-text-right"><div>Interest </div>(Date Due)</th>
            <th class="cell-text-right"><div>Penalty </div> (Date Due)</th>
            <th class="cell-text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($loans['data'] as $key => $value)
            <tr>
                <td>{{ Illuminate\Support\Str::limit($value->loan_officer_first_name, 12)  }}

                </td>
                <td>{{  Illuminate\Support\Str::limit($value->member_first_name, 12) }}</td>
                <td>{{ Illuminate\Support\Str::limit($value->member_phone, 13) }}</td>

                <td class="cell-text-right"><div>{{ $value->pendingPrincipal }}</div>
                   <span class="small-text">
                       {{!empty($value->principalDueDate) ?  '('. $value->principalDueDate.')' : ''}}
                   </span>
                </td>

                <td class="cell-text-right"><div>{{ $value->pendingInterest }}</div>
                    <span class="small-text">
                        {{!empty($value->interestDueDate) ?  '('. $value->interestDueDate.')' : ''}}
                    </span>
                </td>

                <td class="cell-text-right"><div>{{ $value->pendingPenalty }}</div>
                    <span class="small-text">
                        {{!empty($value->penaltyDueDate) ?  '('. $value->penaltyDueDate.')' : ''}}
                    </span>
                </td>

                <td class="cell-text-right">{{ $value->totalDue }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection