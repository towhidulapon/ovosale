{{-- @extends($activeTemplate . 'layouts.app')
@section('app-content')
    @include('Template::partials.auth_header')
    <div class="py-5">
        @yield('content')
    </div>
    @include('Template::partials.footer')
@endsection --}}


@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <main class="dashboard">
        @include('Template::partials.sidenav')
        <section class="dashboard__area">
            <div class="container-fluid">
                @include('Template::partials.topnav')
                <div class="dashboard__area-header flex-wrap gap-2">
                    <h3 class="page-title">{{ __($pageTitle) }}</h3>
                    <div class="breadcrumb-plugins">
                        @stack('breadcrumb-plugins')
                    </div>
                </div>
                <div class="dashboard__area-inner p-0">
                    @yield('panel')
                </div>
            </div>
        </section>
    </main>
@endsection