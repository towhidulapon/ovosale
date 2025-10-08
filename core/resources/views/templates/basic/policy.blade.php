@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <main class="account">
        <span class="account__overlay bg-img dark-bg" data-background-image="{{ asset('assets/admin/images/login-dark.png') }}"></span>
        <span class="account__overlay bg-img light-bg" data-background-image="{{ asset('assets/admin/images/login-bg.png') }}"></span>
        <div class="container py-5">
            <div class="row">
                <div class="col-md-12">
                    @php
    echo $policy->data_values->details;
                    @endphp
                </div>
            </div>
        </div>
    </main>
@endsection
