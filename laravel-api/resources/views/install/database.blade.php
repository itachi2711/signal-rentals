@extends('install.layouts.master')

@section('title', 'Database')
@section('container')
    {!! Form::open(array('url' => url('install/database'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                {!! Form::label('port', 'Port', array('class'=>'')) !!}
                {!! Form::text('port', 3306, array('class' => 'form-control','required'=>'required')) !!}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {!! Form::label('host', 'Host', array('class'=>'')) !!}
                {!! Form::text('host', 'localhost', array('class' => 'form-control','required'=>'required')) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('name', 'Database Name', array('class'=>'')) !!}
        {!! Form::text('name', null, array('class' => 'form-control','required'=>'required')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('username', 'Username', array('class'=>'')) !!}
        {!! Form::text('username', null, array('class' => 'form-control','required'=>'required')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('password', 'Password', array('class'=>'')) !!}
        {!! Form::text('password', null, array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-info pull-right"> Continue</button>
    </div>

    {!! Form::close() !!}
@endsection