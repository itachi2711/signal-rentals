@extends('reports.layouts.master')
@section('title', 'Trial Balance')
@section('footer')
    @include('reports.layouts.footer', ['setting' => $setting])
@endsection
@section('header')
    @include('reports.layouts.header', ['setting' => $setting])
@endsection
@section('title-content')
    <table  width="100%">
        <tr>
            <td align="left" class="cell-title-medium">
                <strong>Trial Balance</strong>
            </td>
            <td align="center" class="cell-title-large cell-text-left">
                Branch Name:  {{ Illuminate\Support\Str::limit($branchName, 20)}}
            </td>
            <td class="cell-title-medium cell-text-left">
            </td>
        </tr>
    </table>
@endsection

@section('main-content')
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Account</th>
            <th class="cell-text-right">Debit</th>
            <th class="cell-text-right">Credit</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pageData as $row)
            @if($loop->last)
                <tr>
                    <th>{{$row[0]}}</th>
                    <th class="cell-text-right">{{ $row[1] }}</th>
                    <th class="cell-text-right">{{ $row[2] }}</th>
                </tr>
            @else
                <tr>
                    <td>{{$row[0]}}</td>
                    <td class="cell-text-right">{{ $row[1] }}</td>
                    <td class="cell-text-right">{{ $row[2] }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@endsection