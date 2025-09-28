@extends('admin.layouts.app')
@section('panel')
@include('admin.reports.product_wise_profit_widget')
<div class="col-12">
    <x-admin.ui.card class="table-has-filter" >
        <x-admin.ui.card.body :paddingZero="true" >
          <x-admin.ui.table.layout filterBoxLocation="reports.filter_form" :hasRecycleBin="false" :renderExportButton="false">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Product Name')</th>
                            <th>@lang('Sales Quantity	')</th>
                            <th>@lang('Sales Price')</th>
                            <th>@lang('Purchase Price')</th>
                            <th>@lang('Gross Profit')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
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
                         <x-admin.ui.table.empty_message/>
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($productsWise->hasPages())
                <x-admin.ui.table.footer>
                    {{ paginateLinks($productsWise) }}
                </x-admin.ui.table.footer>`
            @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>
</div>
@endsection

 
@push('breadcrumb-plugins')
<a href="{{ route('admin.report.profit.invoice_wise') }}" class="btn btn-outline--primary"><i class="las la-file-invoice-dollar"></i> @lang('Invoice Wise Profit')</a>
@endpush

