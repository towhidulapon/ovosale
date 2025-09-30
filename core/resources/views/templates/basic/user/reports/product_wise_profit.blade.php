@extends($activeTemplate . 'layouts.master')
@section('panel')
@include('Template::user.reports.product_wise_profit_widget')
<div class="col-12">
    <x-user.ui.card class="table-has-filter" >
        <x-user.ui.card.body :paddingZero="true" >
          <x-user.ui.table.layout filterBoxLocation="reports.filter_form" :hasRecycleBin="false" :renderExportButton="false">
                <x-user.ui.table>
                    <x-user.ui.table.header>
                        <tr>
                            <th>@lang('Product Name')</th>
                            <th>@lang('Sales Quantity	')</th>
                            <th>@lang('Sales Price')</th>
                            <th>@lang('Purchase Price')</th>
                            <th>@lang('Gross Profit')</th>
                        </tr>
                    </x-user.ui.table.header>
                    <x-user.ui.table.body>
                        @forelse($productsWise as $productWise)
                        <tr>
                            <td>
                                <div>
                                    <strong>
                                       {{ __(@$productWise->product->name), }}
                                    </strong><br>
                                    <span>
                                    @lang('SKU:') {{ __($productWise->sku) }}
                                    </span>
                                </div>
                            </td>
                            <td>{{ @$productWise->total_sales_quantity ?? 0 }}</td>
                            <td>{{ showAmount(@$productWise->total_sales_price) }}</td>
                            <td>{{ showAmount(@$productWise->total_purchase_price) }}</td>
                            <td>{{ showAmount(@$productWise->gross_profit) }}</td>
                        </tr>
                        @empty
                         <x-user.ui.table.empty_message/>
                        @endforelse
                    </x-user.ui.table.body>
                </x-user.ui.table>
                @if ($productsWise->hasPages())
                <x-user.ui.table.footer>
                    {{ paginateLinks($productsWise) }}
                </x-user.ui.table.footer>`
            @endif
            </x-user.ui.table.layout>
        </x-user.ui.card.body>
    </x-user.ui.card>
</div>
@endsection


@push('breadcrumb-plugins')
<a href="{{ route('user.report.profit.invoice_wise') }}" class="btn btn-outline--primary"><i class="las la-file-invoice-dollar"></i> @lang('Invoice Wise Profit')</a>
@endpush

