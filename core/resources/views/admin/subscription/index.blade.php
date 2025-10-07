@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search users" filterBoxLocation="users.filter">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Frequency')</th>
                            <th>@lang('Number of Warehouse')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Trial Days')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($subscriptionPlans as $subscriptionPlan)
                            <tr>
                                <td>
                                    <div>
                                        <strong class="d-block">
                                            {{ $subscriptionPlan->name }}
                                        </strong>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold">
                                            {{ showFrequency($subscriptionPlan->frequency) }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $subscriptionPlan->warehouse_number }}</td>
                                <td>
                                    {{ showAmount($subscriptionPlan->price) }}
                                </td>
                                <td>{{ $subscriptionPlan->trial_days }}</td>
                                <td>
                                    <x-admin.other.status_switch :status="$subscriptionPlan->status" :action="route('admin.subscription.plan.status', $subscriptionPlan->id)" title="subscription plan" />
                                </td>
                                <td>
                                    <a href="{{ route('admin.subscription.plan.edit', $subscriptionPlan->id) }}" class="btn  btn-outline--primary">
                                        <i class="las la-pen"></i> @lang('Edit')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($subscriptionPlans->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($subscriptionPlans) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-permission_check permission="add sale">
        <x-admin.ui.btn.add href="{{ route('admin.subscription.plan.create') }}" text="New Plan" />
    </x-permission_check>
@endpush