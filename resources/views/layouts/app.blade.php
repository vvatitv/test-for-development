<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if( !Auth::guest()  )
        <meta name="api-token" content="{{ Auth::user()->api_token }}">
    @endif
    {!! SEOMeta::generate() !!}
    @if( !empty(Request::input()) )
        <link rel="canonical" href="{{ url(Request::path()) }}">
    @endif
    @stack('styles')
    <link href="{{ asset(mix('storage/css/app.min.css')) }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('storage/images/favicon.ico') }}">
    <!--[if IE]><link rel="shortcut icon" href="{{ asset('storage/images/favicon.ico') }}" type="image/x-icon"><![endif]-->
    <!--[if lt IE 9]><script src="https://cdn.jsdelivr.net/npm/html5shiv.min.js@3.7.2/html5shiv.min.js" type="text/javascript"></script><![endif]-->
    <!--[if lt IE 9]><script src="https://cdn.jsdelivr.net/npm/respond.min.js@1.4.2/respond.min.js" type="text/javascript"></script><![endif]-->
</head>
<body>
    <main class="main" id="app">
        <components-header></components-header>
        <div class="main-body-container">
            @yield('content')
        </div>
        <components-footer></components-footer>
    </main>
    @if( !Auth::guest()  )
    <script>
        window.URLprevious = '{{ \URL::previous() }}';
        window.URLcurrent = '{{ \URL::current() }}';
        window.isAuthenticated = true;
        window.huid = '{{ Auth::user()->slug }}';
        window.suid = '{{ isset(Request::cookie(env("SESSION_COOKIE"))[config("session.cookie")]) ? Request::cookie(env("SESSION_COOKIE"))[config("session.cookie")] : null }}';
    </script>
    @else
    <script>
        window.URLprevious = '{{ \URL::previous() }}';
        window.URLcurrent = '{{ \URL::current() }}';
        window.isAuthenticated = false;
        window.suid = '{{ isset(Request::cookie(env("SESSION_COOKIE"))[config("session.cookie")]) ? Request::cookie(env("SESSION_COOKIE"))[config("session.cookie")] : null }}';
    </script>
    @endif
    @stack('scripts')
        <script src="{{ asset(mix('storage/js/app.min.js')) }}"></script>
    @if( !App::isLocal() )
        @if( !empty(env('JIVOSITE_WIDGET_ID', '')) )
            <script src="//code-ya.jivosite.com/widget/{{ env('JIVOSITE_WIDGET_ID', '') }}" async></script>
        @endif
    @endif
    @if( App::isLocal() && env('MIX_USE_BROWSERSYNC', 'false') == 'true' )
        <script id="__bs_script__">//<![CDATA[
            document.write("<script async src='https://HOST:3000/browser-sync/browser-sync-client.js?v=2.27.10'><\/script>".replace("HOST", location.hostname));
    //]]></script>
    @endif
</body>
</html>