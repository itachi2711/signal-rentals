@extends('install.layouts.master')

@section('title', 'Database tables setup ...')
@section('container')

    @if($alreadyInstalled)

        <div class="alert alert-danger">Signal Rentals is already installed.</div>
        <div class="alert alert-warning">For a new installation, delete database tables and re-run this installer.</div>

        @else

        <div class="alert alert-info">Click install to setup database tables plus add some needed seed data.</div>
        <div class="alert alert-warning">This might take some moment</div>
        {!! Form::open(array('url' => url('install/installation'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
        <div class="form-group">
            <button type="submit" class="btn btn-info pull-right"> Install </button>
        </div>
        {!! Form::close() !!}

    @endif

@endsection
