@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <div class="table-layout">
                        <div class="table-header">
                            <div class="table-search">
                                <input form="filter-form" type="search" name="search" placeholder="@lang('Search Here')"
                                    value="{{ request()->search ?? '' }}" class="form-control form--control-sm">
                                <button form="filter-form" type="submit" class="search-btn"> <i class="las la-search"></i>
                                </button>
                            </div>
                            <div class="table-right">
                                <div class="table-filter">
                                    <div class=" dropdown">
                                        <button class="btn btn-outline--secondary w-100  dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                            <span class="icon">
                                                <i class="las la-sort"></i>
                                            </span>
                                            @lang('Filter')
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-filter-box">
                                            @include('admin.reports.stock_filter_form')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Product')</th>
                                    <th>@lang('Warehouse')</th>
                                    <th>@lang('Stock')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex  gap-2 flex-wrap">
                                                <span class="table-thumb">
                                                    <img src="{{ @$product->product->image_src }}">
                                                </span>
                                                <div>
                                                    <span class="d-block text-start">
                                                        {{ strLimit(__(@$product->product->name), 40) }}
                                                        @if (@$product->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                                            <strong>{{ __(@$product->attribute->name) }}</strong>
                                                            -
                                                            <strong>{{ __(@$product->variant->name) }}</strong>
                                                        @endif
                                                    </span>
                                                    <span>
                                                        {{ __(@$product->sku) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span> {{ __(@$selectWarehouse->name) }} </span> <br>
                                                <span> {{ __(@$selectWarehouse->contact_number) }} </span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $stock = $product->product_stock_sum_stock ?? 0;
                                            @endphp
                                            @if ($stock <= @$product->alert_quantity)
                                                <span class=" badge badge--danger">
                                                    {{ $stock }} {{ __(@$product->product->unit->short_name) }}
                                                </span>
                                            @else
                                                <span class=" badge badge--success">
                                                    {{ $stock }} {{ __(@$product->product->unit->short_name) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($products->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($products) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </div>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
