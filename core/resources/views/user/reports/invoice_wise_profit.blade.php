@extends('admin.layouts.app')
@section('panel')
    @include('admin.reports.invoice_wise_profit_widget')
    <div class="col-12">
        <x-admin.ui.card class="table-has-filter">
            <x-admin.ui.card.body :paddingZero="true">
                <x-admin.ui.table.layout filterBoxLocation="reports.filter_form" :hasRecycleBin="false" :renderExportButton="false">
                    <x-admin.ui.table>
                        <x-admin.ui.table.header>
                            <tr>
                                <th>@lang('Invoice Number') | @lang('Sales Date')</th>
                                <th>@lang('Customer Name')</th>
                                <th>@lang('Sales Price')</th>
                                <th>@lang('Purchase Price')</th>
                                <th>@lang('Gross Profit')</th>
                            </tr>
                        </x-admin.ui.table.header>
                        <x-admin.ui.table.body>
                            @forelse($invoicesWise as $invoiceWise)
                                <tr>
                                    <td>
                                        <div>
                                            <a href="{{ route('admin.sale.view', $invoiceWise->id) }}">{{ $invoiceWise->invoice_number }}</a><br>
                                            {{ showDateTime($invoiceWise->sale_date) }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ __(@$invoiceWise->customer->name) }}
                                    </td>
                                    <td>{{ showAmount(@$invoiceWise->total_sales_price) }}</td>
                                    <td>{{ showAmount(@$invoiceWise->total_purchase_price) }}</td>
                                    <td>{{ showAmount(@$invoiceWise->gross_profit) }}</td>
                                </tr>
                            @empty
                                <x-admin.ui.table.empty_message />
                            @endforelse
                        </x-admin.ui.table.body>
                    </x-admin.ui.table>
                    @if ($invoicesWise->hasPages())
                        <x-admin.ui.table.footer>
                            {{ paginateLinks($invoicesWise) }}
                        </x-admin.ui.table.footer>
                    @endif
                </x-admin.ui.table.layout>
            </x-admin.ui.card.body>
        </x-admin.ui.card>
    </div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.report.profit.product_wise') }}" class="btn btn-outline--primary"><i class="las la-file-invoice-dollar"></i> @lang('Product Wise Profit') </a>
@endpush
