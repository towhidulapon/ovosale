@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout filterBoxLocation="reports.sale_filter_form" :hasRecycleBin="false" :renderExportButton="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Invoice Number') | @lang('Total Items')</th>
                                    <th>@lang('Sale Date') | @lang('Created At')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Total Amount') | @lang('Purchase Value')</th>
                                    <th>@lang('Profit/Loss Amount')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($sales as $sale)
                                    @php
                                        $totalSaleAmount  = $sale->total;
                                        $purchaseAmount   = $sale->total_purchase_value;
                                        $profitLossAmount = $totalSaleAmount - $purchaseAmount;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    <a
                                                        href="{{ route('admin.sale.view', $sale->id) }}">{{ __($sale->invoice_number) }}</a>
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
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($sales->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($sales) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
