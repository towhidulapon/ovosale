@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row gy-4">
        @forelse ($subscriptionPlans as $subscriptionPlan)
            <div class="col-lg-4">
                <x-user.ui.card class="h-100">
                    <x-user.ui.card.header>
                        <h4 class="card-title">{{ __($subscriptionPlan->name) }}</h4>
                    </x-user.ui.card.header>
                    <x-user.ui.card.body>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <span class="fw-bold">@lang('Frequency')</span> -
                                {{ showFrequency($subscriptionPlan->frequency) }}
                            </li>
                            <li class="list-group-item">
                                <span class="fw-bold">@lang('Price')</span> -
                                {{ showAmount($subscriptionPlan->price) }}
                            </li>
                            <li class="list-group-item">
                                <span class="fw-bold">@lang('No of Warehouse')</span> -
                                {{ $subscriptionPlan->warehouse_number }}
                            </li>
                            <li class="list-group-item">
                                <span class="fw-bold">@lang('Trial Days')</span> -
                                {{ $subscriptionPlan->trial_days }}
                            </li>
                        </ul>
                        <a href="{{ route('user.subscription.plan.trial', $subscriptionPlan->id) }}" class="btn btn--warning w-100 mb-2 @if ($trialPlan?->subscription_plan_id == $subscriptionPlan->id) disabled
                        @endif">@lang('Start Trial')
                        </a>
                        @if($subscribedPlan?->subscription_plan_id != $subscriptionPlan->id)
                            <a href="{{ route('user.subscription.plan.purchase', $subscriptionPlan->id) }}" class="btn btn--primary w-100">@lang('Select Plan')
                            </a>
                        @endif
                    </x-user.ui.card.body>
                </x-user.ui.card>
            </div>
        @empty
            <x-user.ui.table.empty_message />
        @endforelse
    </div>
@endsection