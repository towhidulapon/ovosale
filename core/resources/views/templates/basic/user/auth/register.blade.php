@extends($activeTemplate . 'layouts.app')

@section('app-content')
    @if (gs('registration'))
        <main class="account">
            <span class="account__overlay bg-img dark-bg" data-background-image="{{ asset('assets/admin/images/login-dark.png') }}"></span>
            <span class="account__overlay bg-img light-bg" data-background-image="{{ asset('assets/admin/images/login-bg.png') }}"></span>

            <div class="account__card">
                <div class="account__logo">
                    <img src="{{ siteLogo() }}" class="light-show" alt="brand-thumb">
                    <img src="{{ siteLogo('dark') }}" class="dark-show" alt="brand-thumb">
                </div>

                <h2 class="account__title">@lang('Create an Account') ✨</h2>

                @include($activeTemplate . 'partials.social_login')

                <form action="{{ route('user.register') }}" method="POST" class="account__form verify-gcaptcha">
                    @csrf
                    <div class="row g-3">
                        @if (session()->get('reference') != null)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="referenceBy" class="form--label">@lang('Reference by')</label>
                                    <input type="text" name="referBy" id="referenceBy" class="form--control h-48" value="{{ session()->get('reference') }}" readonly>
                                </div>
                            </div>
                        @endif

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form--label">@lang('First Name')</label>
                                <input type="text" class="form--control h-48" name="firstname" value="{{ old('firstname') }}" required>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Last Name')</label>
                                <input type="text" class="form--control h-48" name="lastname" value="{{ old('lastname') }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form--label">@lang('E-Mail Address')</label>
                                <input type="email" class="form--control h-48 checkUser" name="email" value="{{ old('email') }}" required>
                                <span class="exists-error d-none"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Password')</label>
                                <input type="password" class="form--control h-48" name="password" required>
                                <x-strong-password />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Confirm Password')</label>
                                <input type="password" class="form--control h-48" name="password_confirmation" required>
                            </div>
                        </div>

                        <x-captcha />
                    </div>

                    @if (gs('agree'))
                        @php
                            $policyPages = getContent('policy_pages.element', false, orderById: true);
                        @endphp
                        <div class="form-group mt-3">
                            <input type="checkbox" id="agree" @checked(old('agree')) name="agree" required>
                            <label for="agree">@lang('I agree with')</label>
                            <span>
                                @foreach ($policyPages as $policy)
                                    <a href="{{ route('policy.pages', $policy->slug) }}" target="_blank">{{ __($policy->data_values->title) }}</a>
                                    @if (!$loop->last),@endif
                                @endforeach
                            </span>
                        </div>
                    @endif

                    <div class="form-group mt-3">
                        <button type="submit" id="recaptcha" class="btn btn--primary w-100 h-48 fs-16">
                            <i class="fa-solid fa-user-plus"></i> @lang('Register')
                        </button>
                    </div>

                    <p class="mb-0 mt-2">
                        @lang('Already have an account?')
                        <a href="{{ route('user.login') }}">@lang('Login')</a>
                    </p>
                </form>
            </div>
        </main>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif
@endsection


@if (gs('registration'))
    @push('style')
        <style>
            .social-login-btn {
                border: 1px solid #cbc4c4;
            }
        </style>
    @endpush

    @push('script')
        <script>
            "use strict";
            (function ($) {
                $('.checkUser').on('focusout', function () {
                    var url = "{{ route('user.checkUser') }}";
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';
                    var data = { email: value, _token: token };

                    $.post(url, data, function (response) {
                        if (response.data == true) {
                            $(".exists-error").html(`
                                        @lang('You’re already part of our community!')
                                        <a class="ms-1" href="{{ route('user.login') }}">@lang('Login now')</a>
                                    `).removeClass('d-none').addClass("text--danger mt-1 d-block");
                            $(`button[type=submit]`).attr('disabled', true).addClass('disabled');
                        } else {
                            $(".exists-error").empty().addClass('d-none').removeClass("text--danger mt-1 d-block");
                            $(`button[type=submit]`).attr('disabled', false).removeClass('disabled');
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush
@endif





{{-- @extends($activeTemplate . 'layouts.app')
@section('app-content')
    @if (gs('registration'))
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-6">
                    <div class="card ">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Register')</h5>
                        </div>
                        <div class="card-body">
                            @include($activeTemplate . 'partials.social_login')
                            <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha ">
                                @csrf
                                <div class="row">
                                    @if (session()->get('reference') != null)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="referenceBy" class="form-label">@lang('Reference by')</label>
                                                <input type="text" name="referBy" id="referenceBy"
                                                    class="form-control form--control"
                                                    value="{{ session()->get('reference') }}" readonly>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group col-sm-6">
                                        <label class="form-label">@lang('First Name')</label>
                                        <input type="text" class="form-control form--control" name="firstname"
                                            value="{{ old('firstname') }}" required>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form-label">@lang('Last Name')</label>
                                        <input type="text" class="form-control form--control" name="lastname"
                                            value="{{ old('lastname') }}" required>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('E-Mail Address')</label>
                                            <input type="email" class="form-control form--control checkUser"
                                                name="email" value="{{ old('email') }}" required>
                                            <span class="exists-error d-none"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Password')</label>
                                            <input type="password" class="form-control form--control" name="password"
                                                required>
                                            <x-strong-password />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Confirm Password')</label>
                                            <input type="password" class="form-control form--control"
                                                name="password_confirmation" required>
                                        </div>
                                    </div>
                                    <x-captcha />
                                </div>

                                @if (gs('agree'))
                                    @php
        $policyPages = getContent('policy_pages.element', false, orderById: true);
                                    @endphp
                                    <div class="form-group">
                                        <input type="checkbox" id="agree" @checked(old('agree')) name="agree"
                                            required>
                                        <label for="agree">@lang('I agree with')</label> <span>
                                            @foreach ($policyPages as $policy)
                                                <a href="{{ route('policy.pages', $policy->slug) }}"
                                                    target="_blank">{{ __($policy->data_values->title) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </span>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <button type="submit" id="recaptcha" class="btn btn--base w-100">
                                        @lang('Register')
                                    </button>
                                </div>
                                <p class="mb-0">
                                    @lang('Already have an account?') <a href="{{ route('user.login') }}">@lang('Login')</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif

@endsection
@if (gs('registration'))
    @push('style')
        <style>
            .social-login-btn {
                border: 1px solid #cbc4c4;
            }
        </style>
    @endpush

    @push('script')
        <script>
            "use strict";
            (function($) {

                $('.checkUser').on('focusout', function(e) {
                    var url = "{{ route('user.checkUser') }}";
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        if (response.data == true) {
                            $(".exists-error").html(`
                                @lang('You’re already part of our community!')
                                <a class="ms-1" href="{{ route('user.login') }}">@lang('Login now')</a>
                            `).removeClass('d-none').addClass("text--danger mt-1 d-block");
                            $(`button[type=submit]`).attr('disabled', true).addClass('disabled');
                        } else {
                            $(".exists-error").empty().addClass('d-none').removeClass(
                                "text--danger mt-1 d-block");
                            $(`button[type=submit]`).attr('disabled', false).removeClass('disabled');
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush
@endif --}}