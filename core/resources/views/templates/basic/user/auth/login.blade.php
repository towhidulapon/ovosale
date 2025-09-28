@extends('admin.layouts.master')
@section('content')
    <main class="account">
        <span class="account__overlay bg-img dark-bg" data-background-image="{{ asset('assets/admin/images/login-dark.png') }}"></span>
        <span class="account__overlay bg-img light-bg" data-background-image="{{ asset('assets/admin/images/login-bg.png') }}"></span>
        <div class="account__card">
            <div class="account__logo">
                <img src="{{ siteLogo() }}" class="light-show" alt="brand-thumb">
                <img src="{{ siteLogo('dark') }}" class="dark-show" alt="brand-thumb">
            </div>
            <h2 class="account__title">@lang('Welcome Back') 👋</h2>
            <p class="account__desc">@lang('Please enter your credentials to proceed to the next step.')</p>
            <form action="{{ route('admin.login') }}" method="POST" class="account__form verify-gcaptcha">
                @csrf
                <div class="form-group">
                    <label class="form--label">@lang('Username')</label>
                    <input type="text" class="form--control h-48" value="{{ old('username') }}" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form--label">@lang('Password')</label>
                    <div class="position-relative">
                        <input id="password" name="password" required type="password" class="form--control h-48">
                        <span class="password-show-hide fas toggle-password fa-eye-slash" id="#password"></span>
                    </div>
                </div>
                <x-captcha :isAdmin=true />
                <div class="form-group">
                    <button type="submit" class="btn btn--primary w-100  h-48 mb-2 fs-16">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> @lang('Login')
                    </button>
                    <a href="{{ route('admin.password.reset') }}" class="forgot-password">
                        @lang('Forgot your password')?
                    </a>
                </div>
            </form>
        </div>
    </main>
@endsection






{{-- @extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-5">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Login')</h5>
                    </div>
                    <div class="card-body">
                        @include($activeTemplate . 'partials.social_login')
                        <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
                            @csrf
                            <div class="form-group">
                                <label for="email" class="form-label">@lang('Username or Email')</label>
                                <input type="text" name="username" value="{{ old('username') }}"
                                    class="form-control form--control" required>
                            </div>

                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <label for="password" class="form-label mb-0">@lang('Password')</label>
                                    <a class="fw-bold forgot-pass" href="{{ route('user.password.request') }}">
                                        @lang('Forgot your password?')
                                    </a>
                                </div>
                                <input id="password" type="password" class="form-control form--control" name="password"
                                    required>
                            </div>

                            <x-captcha />

                            <div class="form-group form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    @lang('Remember Me')
                                </label>
                            </div>

                            <div class="form-group">
                                <button type="submit" id="recaptcha" class="btn btn--base w-100">
                                    @lang('Login')
                                </button>
                            </div>
                            <p class="mb-0">@lang('Don\'t have any account?') <a href="{{ route('user.register') }}">@lang('Register')</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}
