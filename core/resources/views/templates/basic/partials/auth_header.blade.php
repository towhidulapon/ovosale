<nav class="navbar text-white bg-primary navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <h1 class="text-white">@lang('Logo')</h1>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('user.home') }}">@lang('Dashboard')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('user.deposit.history') }}">@lang('Deposit')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('user.withdraw.history') }}">@lang('Withdraw')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('ticket.index') }}">
                        @lang('My Ticket')
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('user.transactions') }}">
                        @lang('Transactions')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('user.profile.setting') }}">
                        @lang('Profile Setting')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('user.change.password') }}">
                        @lang('Change Password')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('user.twofactor') }}">@lang('2FA Security')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('user.logout') }}">@lang('Logout')</a>
                </li>
            </ul>
        </div>
        @if (gs('multi_language'))
            <div class="ml-auto">
                @php
                    $appLocal = strtoupper(config('app.locale')) ?? 'en';
                @endphp
                <select class=" form-control form--control form-select langSel">
                    @foreach ($languages as $language)
                        <option value="{{ $language->code }}" @selected($appLocal == strtoupper($language->code))>{{ $language->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</nav>
