<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> @yield('title')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/install.css') }}" rel="stylesheet">
</head>
<body class="master">
<div class="box">
    <div class="header">
        <h1 class="header__title">Signal Rentals - Setup</h1>
    </div>

    <div class="main">
        <h2>@yield('title')</h2>
        <hr>
        <div class="row">
            <div class="col-md-12">
                @if(\Illuminate\Support\Facades\Session::has('message'))
                    <div class="alert alert-danger }}">
                        {{ \Illuminate\Support\Facades\Session::get("message") }}
                    </div>
                @endif
                @yield('container')
            </div>
        </div>
    </div>
</div>
</body>
</html>
