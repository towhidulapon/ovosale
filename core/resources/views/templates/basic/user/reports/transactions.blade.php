@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card class="table-has-filter">
                <x-user.ui.card.body :paddingZero="true">
                    <x-user.ui.table.layout filterBoxLocation="reports.transaction_filter_form" :hasRecycleBin="false">
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Account')</th>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    {{ __(@$trx->paymentAccount->account_name) }} -
                                                    {{ __(@$trx->paymentAccount->account_number) }}
                                                </span>
                                                <span
                                                    class="d-block">{{ __(@$trx->paymentAccount->paymentType->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $trx->trx }}</strong>
                                        </td>
                                        <td>
                                            {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                                {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ showAmount($trx->post_balance) }}
                                        </td>

                                        <td>{{ __($trx->details) }}</td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($transactions->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($transactions) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>
@endsection
