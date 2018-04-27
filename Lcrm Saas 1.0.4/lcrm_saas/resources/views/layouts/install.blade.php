<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <title>{{trans('install.installation')}} | LCRM</title>
    @include('layouts.header._assets')
    <link href="{{ asset('css/custom_install.css') }}" rel="stylesheet" type="text/css"/>
    @yield('styles')
</head>
<body>
<div id="page-wrapper">
    <div>
        <div class="top_logo">
            <div class="header_padd">
                <img src="{{ url('img/logo.png') }}" alt="LCRM" class="logo center-block install_header_logo">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="wizard wizard_section">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/libs.js') }}" type="text/javascript"></script>
@yield('scripts')
<script>
    $(".selected").parent('li').addClass('active');
    $(".selected.done").parent("li").removeClass("active");
</script>

</body>
</html>
