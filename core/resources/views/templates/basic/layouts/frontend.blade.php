@extends($activeTemplate . 'layouts.app')
@section('app-content')
    @stack('fbComment')
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        @yield('content')
    </div>
@endsection