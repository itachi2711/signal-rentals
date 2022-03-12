@extends('install.layouts.master')

@section('title', 'Server Requirements')
@section('container')

    <table width="100%" class="table-striped">
        @foreach($requirements as $extension => $enabled)
                <tr>
               <td>{{ $extension }}</td>
                @if($enabled)
                    <td class="badge badge1"><i class="fa fa-check"></i></td>
                @else
                    <td class="badge badge2"><i class="fa fa-times"></i></td>
                @endif
                </tr>
        @endforeach
    </table>
<br/>

    <div class="form-group">
        @if($allSet)
            <a href="{{ url('install/permissions') }}"
               class="btn btn-info pull-right">Continue</a>
        @else
            <div class="alert alert-danger">Error. Fix marked requirements to continue.</div>
            <div class="alert alert-warning">Hint. Consult your server provider if in doubt.</div>
            <a class="btn btn-info pull-right" href="{{ \Illuminate\Support\Facades\Request::url() }}">
                Refresh
                <i class="fa fa-refresh"></i></a>
        @endif

    </div>
@endsection