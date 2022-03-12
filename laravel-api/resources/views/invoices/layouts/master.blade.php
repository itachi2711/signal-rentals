<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
   {{-- @include('invoices.styles.bootstrap-styles')
    @include('invoices.styles.report-styles')--}}
    @include('invoices.styles.invoice-style')
    <title>@yield('title')</title>
</head>
<body>
{{--<footer>
    @yield('footer')
</footer>

<div class="information">
    @yield('header')
    @yield('title-content')
</div>--}}

<br/>

<div class="content">
    @yield('main-content')
</div>
<script type="text/php">
if ( isset($pdf) ) {
    $pdf->page_script('
        if ($PAGE_COUNT > 1) {
            $font = $fontMetrics->get_font("Verdana, Arial, sans-serif", "normal");
            $size = 10;
            $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
            $y = 820;
            $x = 520;
            $pdf->text($x, $y, $pageText, $font, $size);
        }
    ');
}
</script></body></html>
