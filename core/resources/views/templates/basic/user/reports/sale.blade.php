@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card class="table-has-filter">
                <x-user.ui.card.body :paddingZero="true">
                    <x-user.ui.table.layout filterBoxLocation="reports.sale_filter_form" :hasRecycleBin="false" :renderExportButton="false">
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Invoice Number') | @lang('Total Items')</th>
                                    <th>@lang('Sale Date') | @lang('Created At')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Total Amount') | @lang('Purchase Value')</th>
                                    <th>@lang('Profit/Loss Amount')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($sales as $sale)
                                    @php
                                        $totalSaleAmount = $sale->total;
                                        $purchaseAmount = $sale->total_purchase_value;
                                        $profitLossAmount = $totalSaleAmount - $purchaseAmount;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    <a
                                                        href="{{ route('user.sale.view', $sale->id) }}">{{ __($sale->invoice_number) }}</a>
                                                </span>
                                                <span>{{ __($sale->sale_details_count) }} @lang('Items') </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showDateTime($sale->sale_date, 'Y-m-d') }}</span>
                                                <span>{{ showDateTime($sale->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __(@$sale->customer->name) }}</span>
                                                <span>{{ __(@$sale->customer->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block text--success">{{ showAmount($totalSaleAmount) }}</span>
                                                <span class="text--warning">{{ showAmount($purchaseAmount) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="@if ($profitLossAmount <= 0) text--danger @else text--success @endif">
                                                {{ showAmount($totalSaleAmount - $purchaseAmount) }}
                                            </span>
                                        </td>

                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($sales->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($sales) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>
@endsection
