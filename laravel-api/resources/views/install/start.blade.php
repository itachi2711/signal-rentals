@extends('install.layouts.master')

@section('title', 'Guidelines')
@section('container')

   <p> 1. We check server requirement.</p>

   <p>  2. We check directory permissions.</p>

   <p>  3. You provide database name, user and password.</p>

   <p>  - This MYSQL database should be created manually before this step.</p>

    <div class="form-group">
        <a href="{{ url('install/requirements') }}"
           class="btn btn-info pull-right">Lets Start ...</a>
    </div>
@endsection