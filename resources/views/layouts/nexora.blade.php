
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- CSS files -->
    <link href="{{ asset('dist/css/nexora.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/custom-colors.css') }}" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --nexora-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>

    <!-- Custom CSS for specific page.  -->
    @stack('page-styles')
    @livewireStyles
</head>
    <body>

        <div class="page">

            @include('layouts.body.header')

            @include('layouts.body.navbar')

            <div class="page-wrapper">
                <div>
                    @yield('content')
                </div>

                @include('layouts.body.footer')
            </div>
        </div>

        <!-- Nexora Core -->
        <script src="{{ asset('dist/js/nexora.min.js') }}" defer></script>
        {{--- Page Scripts ---}}
        @stack('page-scripts')

        @livewireScripts
    </body>
</html>
