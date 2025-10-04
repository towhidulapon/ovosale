@extends('admin.layouts.app')
@section('panel')
    @php
        $prefix = gs('prefix_setting');
    @endphp
    <form method="POST" action="{{ route('admin.setting.prefix.update') }}">
        <x-admin.ui.card>
            <x-admin.ui.card.body>
                @csrf
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Purchase Invoice Prefix')</label>
                            <input class="form-control" type="text" name="purchase_invoice_prefix" required
                                value="{{ @$prefix->purchase_invoice_prefix }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Sale Invoice Prefix')</label>
                            <input class="form-control" type="text" name="sale_invoice_prefix" required
                                value="{{ @$prefix->sale_invoice_prefix }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Product Code Prefix')</label>
                            <input class="form-control" type="text" name="product_code_prefix" required
                                value="{{ @$prefix->product_code_prefix }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Stock Transfer Invoice Prefix')</label>
                            <input class="form-control" type="text" name="stock_transfer_invoice_prefix" required
                                value="{{ @$prefix->stock_transfer_invoice_prefix }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <x-admin.ui.btn.submit />
                    </div>
                </div>
            </x-admin.ui.card.body>
        </x-admin.ui.card>
    </form>
@endsection
