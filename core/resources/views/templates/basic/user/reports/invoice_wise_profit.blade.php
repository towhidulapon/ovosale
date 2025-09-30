@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('Template::user.reports.invoice_wise_profit_widget')
    <div class="col-12">
        <x-user.ui.card class="table-has-filter">
            <x-user.ui.card.body :paddingZero="true">
                <x-user.ui.table.layout filterBoxLocation="reports.filter_form" :hasRecycleBin="false" :renderExportButton="false">
                    <x-user.ui.table>
                        <x-user.ui.table.header>
                            <tr>
                                <th>@lang('Invoice Number') | @lang('Sales Date')</th>
                                <th>@lang('Customer Name')</th>
                                <th>@lang('Sales Price')</th>
                                <th>@lang('Purchase Price')</th>
                                <th>@lang('Gross Profit')</th>
                            </tr>
                        </x-user.ui.table.header>
                        <x-user.ui.table.body>
                            @forelse($invoicesWise as $invoiceWise)
                                <tr>
                                    <td>
                                        <div>
                                            <a href="{{ route('user.sale.view', $invoiceWise->id) }}">{{ $invoiceWise->invoice_number }}</a><br>
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
                                <x-user.ui.table.empty_message />
                            @endforelse
                        </x-user.ui.table.body>
                    </x-user.ui.table>
                    @if ($invoicesWise->hasPages())
                        <x-user.ui.table.footer>
                            {{ paginateLinks($invoicesWise) }}
                        </x-user.ui.table.footer>
                    @endif
                </x-user.ui.table.layout>
            </x-user.ui.card.body>
        </x-user.ui.card>
    </div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('user.report.profit.product_wise') }}" class="btn btn-outline--primary"><i class="las la-file-invoice-dollar"></i> @lang('Product Wise Profit') </a>
@endpush
