@extends('admin.layouts.app')
@section('panel')
    @php
        $today = now()->format('Y-m-d');
    @endphp
    <div class="row responsive-row">
        <div class="col-xl-12 col-xxl-12">
            <div class="row responsive-row mb-0">
                <x-permission_check permission="view sale">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four url="{{ route('admin.sale.list') }}?date={{ $today }}"
                            variant="success" title="Today sale" :value="$widget['today_sale']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view sale">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four
                            url="{{ route('admin.sale.list') }}?date={{ now()->startOfWeek()->format('Y-m-d') }}to{{ $today }}"
                            variant="success" title="This Week sale" :value="$widget['this_week_sale']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view sale">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four
                            url="{{ route('admin.sale.list') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
                            variant="success" title="This Month sale" :value="$widget['this_month_sale']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view sale">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four url="{{ route('admin.sale.list') }}" variant="success" title="All sale"
                            :value="$widget['all_sale']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view purchase">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four url="{{ route('user.purchase.list') }}?date={{ $today }}"
                            variant="danger" title="Today Purchase" :value="$widget['today_purchase']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view purchase">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four
                            url="{{ route('user.purchase.list') }}?date={{ now()->startOfWeek()->format('Y-m-d') }}to{{ $today }}"
                            variant="danger" title="This Week Purchase" :value="$widget['this_week_purchase']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view purchase">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four
                            url="{{ route('user.purchase.list') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
                            variant="danger" title="This Month Purchase" :value="$widget['this_month_purchase']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view purchase">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four url="{{ route('user.purchase.list') }}" variant="danger"
                            title="All Purchase" :value="$widget['all_purchase']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view expense">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four url="{{ route('admin.expense.list') }}?date={{ $today }}"
                            variant="primary" title="Today Expense" :value="$widget['today_expense']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view expense">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four
                            url="{{ route('admin.expense.list') }}?date={{ now()->startOfWeek()->format('Y-m-d') }}to{{ $today }}"
                            variant="primary" title="This Week Expense" :value="$widget['this_week_expense']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view expense">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four
                            url="{{ route('admin.expense.list') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
                            variant="primary" title="This Month Expense" :value="$widget['this_month_expense']" icon="las la-calendar" />
                    </div>
                </x-permission_check>
                <x-permission_check permission="view expense">
                    <div class="col-xxl-3 col-sm-6">
                        <x-admin.ui.widget.four url="{{ route('admin.expense.list') }}" variant="primary"
                            title="All Expense" :value="$widget['all_expense']" icon="las la-calendar" />
                    </div>
                </x-permission_check>

                <x-permission_check permission="view product">
                    <div class="col-xl-12 col-xxl-6">
                        <x-admin.ui.card class="mh-420">
                            <x-admin.ui.card.header>
                                <h4 class="card-title">@lang('Top Selling Products')
                                    <x-admin.ui.btn.list href="{{ route('admin.sale.top.selling.product') }}"
                                        text="View More" />
                                </h4>
                            </x-admin.ui.card.header>
                            <x-admin.ui.card.body class="p-0">
                                <div class="table-responsive">
                                    <table class="table product-table">
                                        <thead>
                                            <tr>
                                                <th>@lang('Product')</th>
                                                <th>@lang('Total')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($topSellingProducts as $topSellingProduct)
                                                @php
                                                    $productDetails = $topSellingProduct->productDetail;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div
                                                            class="d-flex align-items-center gap-2 flex-wrap justify-content-start">
                                                            <span class="table-thumb">
                                                                <img src="{{ @$productDetails->product->image_src }}">
                                                            </span>
                                                            <div>
                                                                <strong class="d-block text-start">
                                                                    {{ strLimit(__(@$productDetails->product->name), 10) }}
                                                                    @if (@$productDetails->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                                                        <span>
                                                                            - {{ __(@$productDetails->attribute->name) }}
                                                                            - {{ __(@$productDetails->variant->name) }}
                                                                        </span>
                                                                    @endif
                                                                    <span class="fw-bold d-block">
                                                                        {{ @$productDetails->sku }}
                                                                    </span>
                                                                </strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge--success">
                                                            {{ $topSellingProduct->total_quantity }}
                                                            {{ __(@$productDetails->product->unit->short_name) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-admin.ui.table.empty_message />
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </x-admin.ui.card.body>
                        </x-admin.ui.card>
                    </div>
                </x-permission_check>
                <x-permission_check permission="view product">
                    <div class="col-xl-12 col-xxl-6">
                        <x-admin.ui.card class="mh-420">
                            <x-admin.ui.card.header
                                class="d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                <h4 class="card-title">@lang('Stock Alert')</h4>
                                <div>
                                    <select name="warehouse_id" class="form-select form-control form-select-sm">
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ @$warehouse->id }}">{{ __(@$warehouse->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </x-admin.ui.card.header>
                            <x-admin.ui.card.body class="p-0">
                                <div class="table-responsive">
                                    <table class="product-table table">
                                        <thead>
                                            <tr>
                                                <th>@lang('Product')</th>
                                                <th>@lang('Stock')</th>
                                            </tr>
                                        </thead>
                                        <tbody class="low_stock_product_tbody"></tbody>
                                    </table>
                                </div>
                            </x-admin.ui.card.body>
                        </x-admin.ui.card>
                    </div>
                </x-permission_check>
                <x-permission_check permission="view sale">
                    <div class="col-xl-12 col-xxl-6">
                        <x-admin.ui.card class="h-100">
                            <x-admin.ui.card.header>
                                <h4 class="card-title">@lang('Recent Sales')
                                    <x-admin.ui.btn.list href="{{ route('admin.sale.list') }}" text="View More" />
                                </h4>
                            </x-admin.ui.card.header>
                            <x-admin.ui.card.body class="p-0">
                                <x-admin.ui.table>
                                    <x-admin.ui.table.header>
                                        <tr>
                                            <th>@lang('Invoice Number')</th>
                                            <th>@lang('Customer')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Total')</th>
                                        </tr>
                                    </x-admin.ui.table.header>
                                    <x-admin.ui.table.body>
                                        @forelse($recentSales as $recentSale)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <a
                                                            href="{{ route('admin.sale.view', $recentSale->id) }}">{{ __($recentSale->invoice_number) }}</a><br>
                                                        <span>{{ $recentSale->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ __(@$recentSale->customer->name) }}
                                                </td>
                                                <td> @php echo $recentSale->statusBadge @endphp </td>
                                                <td>
                                                    <span class="d-block">{{ showAmount($recentSale->total) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <x-admin.ui.table.empty_message />
                                        @endforelse
                                    </x-admin.ui.table.body>
                                </x-admin.ui.table>
                            </x-admin.ui.card.body>
                        </x-admin.ui.card>
                    </div>
                </x-permission_check>


                <x-permission_check permission="view purchase">
                    <div class="col-xl-12 col-xxl-6">
                        <x-admin.ui.card class="h-100">
                            <x-admin.ui.card.header>
                                <h4 class="card-title">@lang('Recent Purchases')
                                    <x-permission_check permission="view purchase">
                                        <x-admin.ui.btn.list href="{{ route('user.purchase.list') }}"
                                            text="View More" />
                                    </x-permission_check>
                                </h4>
                            </x-admin.ui.card.header>
                            <x-admin.ui.card.body class="p-0">
                                <x-admin.ui.table>
                                    <x-admin.ui.table.header>
                                        <tr>
                                            <th>@lang('Invoice Number')</th>
                                            <th>@lang('Supplier')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Total')</th>
                                        </tr>
                                    </x-admin.ui.table.header>
                                    <x-admin.ui.table.body>
                                        @forelse($recentPurchases as $recentPurchase)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <a
                                                            href="{{ route('user.purchase.view', $recentPurchase->id) }}">{{ __($recentPurchase->invoice_number) }}</a><br>
                                                        <span>{{ $recentPurchase->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ __(@$recentPurchase->supplier->name) }}
                                                </td>
                                                <td>
                                                    @php echo $recentPurchase->statusBadge @endphp
                                                </td>
                                                <td>
                                                    <span class="d-block">{{ showAmount($recentPurchase->total) }}</span>

                                                </td>
                                            </tr>
                                        @empty
                                            <x-admin.ui.table.empty_message />
                                        @endforelse
                                    </x-admin.ui.table.body>
                                </x-admin.ui.table>
                            </x-admin.ui.card.body>
                        </x-admin.ui.card>
                    </div>
                </x-permission_check>


                <x-permission_check permission="view sale">
                    <div class="col-xl-12 col-xxl-6">
                        <x-admin.ui.card class="h-100">
                            <x-admin.ui.card.header>
                                <h4 class="card-title">@lang('Last 30 Days Sales')</h4>
                            </x-admin.ui.card.header>
                            <x-admin.ui.card.body>
                                <div id="sales-chart"></div>
                            </x-admin.ui.card.body>
                        </x-admin.ui.card>
                    </div>
                </x-permission_check>

                <x-permission_check permission="view purchase">
                    <div class="col-xl-12 col-xxl-6">
                        <x-admin.ui.card class="h-100">
                            <x-admin.ui.card.header>
                                <h4 class="card-title">@lang('Last 30 Days Purchase')</h4>
                            </x-admin.ui.card.header>
                            <x-admin.ui.card.body>
                                <div id="purchase-chart"></div>
                            </x-admin.ui.card.body>
                        </x-admin.ui.card>
                    </div>
                </x-permission_check>

            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <div class="d-flex gap-2 flex-wrap">
        <x-permission_check permission="add sale">
            <a class="btn btn--success" href="{{ route('admin.sale.add') }}">
                <i class="las la-list"></i> @lang('New Sale')
            </a>
        </x-permission_check>
        <x-permission_check permission="add purchase">
            <a class="btn btn--warning" href="{{ route('user.purchase.add') }}">
                <i class="las la-th-list"></i> @lang('New Purchase')
            </a>
        </x-permission_check>
        <x-permission_check permission="add expense">
            <a class="btn btn--danger" href="{{ route('admin.expense.list') }}?popup=yes">
                <i class="las la-file-invoice-dollar"></i> @lang('New Expense')
            </a>
        </x-permission_check>
    </div>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            let salesChart = barChart(
                document.querySelector("#sales-chart"),
                @json(__(gs('cur_text'))),
                [{
                    name: 'Sales',
                    data: []
                }, ],
                [],
                ['#d7f4df'],
            );
            let purchaseChart = barChart(
                document.querySelector("#purchase-chart"),
                @json(__(gs('cur_text'))),
                [{
                    name: 'Purchase',
                    data: []
                }, ],
                [],
                ['#ffd9d7']
            );

            const salesAndPurchaseChart = () => {
                const url = "{{ route('admin.chart.sales.purchase') }}";
                $.get(url,
                    function(data, status) {

                        if (data.status == 'success') {
                            const respData = data.data;
                            salesChart.updateSeries([{
                                data: respData.sales,
                                name: "Sales"
                            }]);

                            purchaseChart.updateSeries([{
                                data: respData.purchase,
                                name: "Purchase"
                            }]);

                            salesChart.updateOptions({
                                xaxis: {
                                    categories: respData.dates,
                                }
                            });
                            purchaseChart.updateOptions({
                                xaxis: {
                                    categories: respData.dates,
                                }
                            });
                        }
                    }
                );
            }
            salesAndPurchaseChart();


            $(`select[name=warehouse_id]`).on('change', function(e) {
                const warehouseId = $(this).val() || 0;
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.low.stock.product') }}",
                    data: {
                        'warehouse_id': warehouseId
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $(".low_stock_product_tbody").html(`
                            <tr class="text-center">
                                <td colspan="100%">
                                    <div class="loading-spinner col-12 text-center"><img
                                            src="{{ asset('assets/images/loadings.gif') }}" alt="Loading...">
                                    </div>
                                </td>
                            </tr>
                        `);
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $(".low_stock_product_tbody").html(response.data.html);
                        }
                    }
                });
            }).change();

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .loading-spinner img {
            max-width: 100px !important;
        }

        .mh-420 {
            min-height: 425px;
        }
    </style>
@endpush
