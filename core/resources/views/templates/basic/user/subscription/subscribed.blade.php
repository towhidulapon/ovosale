@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card>
                <x-user.ui.card.body :paddingZero=true>
                    <x-user.ui.table.layout>
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Plan Name')</th>
                                    <th>@lang('Frequency')</th>
                                    <th>@lang('No of Warehouse')</th>
                                    <th>@lang('Subscribed Date')</th>
                                    <th>@lang('End Date')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($purchasedPlans as $purchasedPlan)
                                    <tr>
                                        <td>
                                            {{ __($purchasedPlan->subscriptionPlan->name) }}
                                        </td>
                                        <td>{{ showFrequency($purchasedPlan->subscriptionPlan->frequency) }}</td>
                                        <td>{{ $purchasedPlan->subscriptionPlan->warehouse_number }}</td>
                                        <td>
                                            {{ showDateTime($purchasedPlan->created_at) }} <br>
                                            {{ diffForHumans(showDateTime($purchasedPlan->created_at)) }}
                                        </td>
                                        <td>
                                            {{ showDateTime(subscriptionEndDate($purchasedPlan->created_at, $purchasedPlan->subscriptionPlan->frequency)) }} <br>
                                            {{ diffForHumans(subscriptionEndDate($purchasedPlan->created_at, $purchasedPlan->subscriptionPlan->frequency)) }}
                                        </td>
                                        <td>
                                            @php echo $purchasedPlan->statusBadge; @endphp
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($purchasedPlans->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($purchasedPlans) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>
@endsection