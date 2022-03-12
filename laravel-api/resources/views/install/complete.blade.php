@extends('install.layouts.master')

@section('title', 'Congratulations !!')
@section('container')
    <p class="paragraph">Installation completed successfully.</p>
    <div class="alert alert-info"><h5>Default User:</h5>
        <p class="paragraph">Email: admin@admin.com</p>
        <p class="paragraph">Password: admin123</p>
    </div>
    <div class="form-group">
        <a href="{{ dirname(url('/')) }}"
           class="btn btn-info pull-right">Login</a>
    </div>
@endsection