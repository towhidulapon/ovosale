@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search users" filterBoxLocation="users.filter">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Username')</th>
                            <th>@lang('Plan Name')</th>
                            <th>@lang('Subscribed Date')</th>
                            <th>@lang('End Date')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($purchasedPlans as $purchasedPlan)
                            <tr>
                                <td>
                                    {{ $purchasedPlan->user->username }}
                                </td>
                                <td>
                                    {{ $purchasedPlan->subscriptionPlan->name }}
                                </td>
                                <td>
                                    {{ $purchasedPlan->created_at }}
                                </td>
                                <td>
                                    {{ showDateTime(subscriptionEndDate($purchasedPlan->created_at, $purchasedPlan->subscriptionPlan->frequency)) }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.subscription.plan.order.details', $purchasedPlan->id) }}" class="btn  btn-outline--primary">
                                        <i class="las la-pen"></i> @lang('Details')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($purchasedPlans->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($purchasedPlans) }}
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