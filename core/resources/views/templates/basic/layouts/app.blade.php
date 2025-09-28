<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')

    <script src="{{ asset($activeTemplateTrue . '/js/theme.js') }}"></script>

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    @stack('style-lib')

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">


    @stack('style')
    <link rel="stylesheet"
        href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}&secondColor={{ gs('secondary_color') }}">
</head>
@php echo loadExtension('google-analytics') @endphp

<body>

    @yield('app-content')

    @include('Template::partials.cookie')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

    @stack('script-lib')

    <script src="{{ asset('assets/global/js/global.js') }}"></script>

    <script src="{{ asset($activeTemplateTrue . '/js/search.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . '/js/main.js') }}"></
    @php echo loadExtension('tawk-chat') @endphp

    @include('partials.notify')

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

</body>
<script>
    (function($) {
        "use strict";

        //plicy
        $('.policy').on('click', function() {
            $.get('{{ route('cookie.accept') }}', function(response) {
                $('.cookies-card').addClass('d-none');
            });
        });

        // event when change lang
        $(".langSel").on("change", function() {
            window.location.href = "{{ route('home') }}/change/" + $(this).val();
        });

        //show cookie card
        setTimeout(function() {
            $('.cookies-card').removeClass('hide');
        }, 2000);
    })(jQuery);
</script>
</body>

</html>
