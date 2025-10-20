@extends('admin.layouts.app')
@section('panel')
    <div class="row  gy-4 justify-content-center">
        <div class="col-xl-4 col-md-6">
            <x-admin.ui.card>
                <x-admin.ui.card.header>
                    <h4 class="card-title">
                        @lang('Plan Details')
                    </h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center ps-0 flex-wrap">
                            <span class="text-muted fs-14">@lang('Username')</span>
                            <span class="fs-14">
                                <a href="{{ route('admin.users.detail', $plan->user_id) }}"><span>@</span>{{ @$plan->user->username }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center ps-0 flex-wrap">
                            <span class="text-muted fs-14">@lang('Plan Name')</span>
                            <span class="fs-14">{{ __($plan->subscriptionPlan->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center ps-0 flex-wrap">
                            <span class="text-muted fs-14">@lang('Date')</span>
                            <span class="fs-14">{{ showDateTime($plan->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center ps-0 flex-wrap">
                            <span class="text-muted fs-14">@lang('End Date')</span>
                            <span class="fs-14">{{ showDateTime(subscriptionEndDate($plan->created_at, $plan->subscriptionPlan->frequency)) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center ps-0 flex-wrap">
                            <span class="text-muted fs-14">@lang('Status')</span>
                            <span class="text-end">
                                @php echo $plan->statusBadge @endphp
                            </span>
                        </li>

                    </ul>
                </x-admin.ui.card.body>
            </x-admin.ui.card>

        </div>
    </div>


    <x-confirmation-modal />
@endsection
