<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@lang('Stock Transfer') - {{ $transfer->invoice_number }}</title>
    <style>
        *,
        *::after,
        *::before {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans;
            line-height: 1.5;
        }

        @page {
            /* size: 8.27in 11.7in; */
            size: 49.625rem 70.188rem;
        }

        .invoice-wrapper {
            font-size: 12px;
            background-color: rgb(255, 255, 255);
        }

        .invoice-header,
        .invoice-body {
            padding: 8px 16px;
        }

        .invoice-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .invoice-logo {
            max-width: 180px;
            display: block;
            object-fit: cover;
        }

        .invoice-logo.dark-show {
            display: none !important;
        }

        .invoice-company-info {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .invoice-company-info__item {
            font-weight: 600;
            line-height: 180%;
            color: #000000;
            line-height: 150%;
        }

        .invoice-company-info__item .label {
            font-weight: 400;
            color: #000000;
        }

        .invoice-customer__title {
            font-size: 14px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 0px;
        }

        .invoice-customer-info {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .invoice-customer-info__item .label {
            font-weight: 400;
            color: #000000;
        }

        .invoice-customer-info__item .value {
            font-weight: 600;
            color: #000000;
        }

        .invoice-info {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .invoice-info__item .label {
            font-weight: 700;
            color: #000000;
        }

        .invoice-info__item .value {
            font-size: inherit;
            font-weight: 500;
            color: #000000;
        }

        .invoice-pdt {
            margin-top: 16px;
        }

        .invoice-pdt__footer {
            margin-top: 16px;
        }

        .invoice-pdt-table-wrapper {
            overflow: hidden;
            border-radius: 6px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .invoice-pdt-table {
            border-collapse: collapse;
            border-spacing: 0px;
            font-weight: 500;
        }

        .invoice-pdt-table thead>tr>th,
        .invoice-pdt-table tbody>tr>td {
            padding: 8px;
            font-weight: 500;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        @media print {

            .invoice-pdt-table thead>tr>th,
            .invoice-pdt-table tbody>tr>td {
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            }
        }

        .invoice-pdt-table thead>tr>th {
            color: #000000;
            font-weight: 600;
            white-space: nowrap;
        }

        .invoice-pdt-table tbody>tr>td {
            color: #000000;
            font-weight: 400;
        }

        .invoice-pdt-table tbody>tr.tr-last>td {
            border: none;
        }

        .invoice-payment {
            margin-left: auto;
        }

        .invoice-payment__title {
            font-size: 14px;
            line-height: 100%;
            font-weight: 700;
            color: #000000;
            margin-bottom: 0px;
            line-height: 1;
        }

        .invoice-payment-info {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .invoice-payment-info__item {
            line-height: 1.3;
        }

        .invoice-payment-info__item .label {
            font-weight: 400;
            color: #000000;
        }

        .invoice-payment-info__item .value {
            font-weight: 600;
            color: #000000;
        }

        .invoice-pricing__title {
            font-size: 14px;
            font-weight: 700;
            color: #000000;
            line-height: 1;
        }

        .invoice-pricing-info {
            max-width: 200px;
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
            text-align: right;
            margin-left: auto;
        }

        .invoice-pricing-info__item {
            white-space: nowrap;
            line-height: 1.3;
        }

        .invoice-pricing-info__item .label {
            color: #000000;
            font-weight: 400;
        }

        .invoice-pricing-info__item .value {
            font-weight: 500;
            color: #000000;
        }

        .invoice-pricing-info__item.total {
            font-size: 24px;
            margin-top: 12px;
            padding-top: 4px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            line-height: 1;
        }

        .invoice-pricing-info__item.total .label,
        .invoice-pricing-info__item.total .value {
            color: #000000;
            font-weight: 500;
        }

        .clearfix::after {
            display: block;
            clear: both;
            content: ""
        }

        .float-start {
            float: left !important;
        }

        .float-end {
            float: right !important;
        }

        .float-none {
            float: none !important;
        }

        .align-top {
            vertical-align: top !important;
        }

        .align-middle {
            vertical-align: middle !important;
        }

        .align-bottom {
            vertical-align: bottom !important;
        }

        .w-25 {
            width: 25% !important;
        }

        .w-50 {
            width: 50% !important;
        }

        .w-75 {
            width: 75% !important;
        }

        .w-100 {
            width: 100% !important;
        }

        .w-auto {
            width: auto !important;
        }

        .m-1 {
            margin: 0.25rem !important;
        }

        .m-2 {
            margin: 0.5rem !important;
        }

        .m-3 {
            margin: 1rem !important;
        }

        .m-4 {
            margin: 1.5rem !important;
        }

        .m-5 {
            margin: 3rem !important;
        }

        .m-auto {
            margin: auto !important;
        }

        .mx-0 {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }

        .mx-1 {
            margin-right: 0.25rem !important;
            margin-left: 0.25rem !important;
        }

        .mx-2 {
            margin-right: 0.5rem !important;
            margin-left: 0.5rem !important;
        }

        .mx-3 {
            margin-right: 1rem !important;
            margin-left: 1rem !important;
        }

        .mx-4 {
            margin-right: 1.5rem !important;
            margin-left: 1.5rem !important;
        }

        .mx-5 {
            margin-right: 3rem !important;
            margin-left: 3rem !important;
        }

        .mx-auto {
            margin-right: auto !important;
            margin-left: auto !important;
        }

        .my-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .my-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }

        .my-2 {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        .my-3 {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }

        .my-4 {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }

        .my-5 {
            margin-top: 3rem !important;
            margin-bottom: 3rem !important;
        }

        .my-auto {
            margin-top: auto !important;
            margin-bottom: auto !important;
        }

        .mt-0 {
            margin-top: 0 !important;
        }

        .mt-1 {
            margin-top: 0.25rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        .mt-4 {
            margin-top: 1.5rem !important;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .mt-auto {
            margin-top: auto !important;
        }

        .me-0 {
            margin-right: 0 !important;
        }

        .me-1 {
            margin-right: 0.25rem !important;
        }

        .me-2 {
            margin-right: 0.5rem !important;
        }

        .me-3 {
            margin-right: 1rem !important;
        }

        .me-4 {
            margin-right: 1.5rem !important;
        }

        .me-5 {
            margin-right: 3rem !important;
        }

        .me-auto {
            margin-right: auto !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .mb-5 {
            margin-bottom: 3rem !important;
        }

        .mb-auto {
            margin-bottom: auto !important;
        }

        .ms-0 {
            margin-left: 0 !important;
        }

        .ms-1 {
            margin-left: 0.25rem !important;
        }

        .ms-2 {
            margin-left: 0.5rem !important;
        }

        .ms-3 {
            margin-left: 1rem !important;
        }

        .ms-4 {
            margin-left: 1.5rem !important;
        }

        .ms-5 {
            margin-left: 3rem !important;
        }

        .ms-auto {
            margin-left: auto !important;
        }

        .p-0 {
            padding: 0 !important;
        }

        .p-1 {
            padding: 0.25rem !important;
        }

        .p-2 {
            padding: 0.5rem !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .p-4 {
            padding: 1.5rem !important;
        }

        .p-5 {
            padding: 3rem !important;
        }

        .px-0 {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        .px-1 {
            padding-right: 0.25rem !important;
            padding-left: 0.25rem !important;
        }

        .px-2 {
            padding-right: 0.5rem !important;
            padding-left: 0.5rem !important;
        }

        .px-3 {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }

        .px-4 {
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
        }

        .px-5 {
            padding-right: 3rem !important;
            padding-left: 3rem !important;
        }

        .py-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .py-1 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
        }

        .py-2 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        .py-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .py-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .py-5 {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }

        .pt-0 {
            padding-top: 0 !important;
        }

        .pt-1 {
            padding-top: 0.25rem !important;
        }

        .pt-2 {
            padding-top: 0.5rem !important;
        }

        .pt-3 {
            padding-top: 1rem !important;
        }

        .pt-4 {
            padding-top: 1.5rem !important;
        }

        .pt-5 {
            padding-top: 3rem !important;
        }

        .pe-0 {
            padding-right: 0 !important;
        }

        .pe-1 {
            padding-right: 0.25rem !important;
        }

        .pe-2 {
            padding-right: 0.5rem !important;
        }

        .pe-3 {
            padding-right: 1rem !important;
        }

        .pe-4 {
            padding-right: 1.5rem !important;
        }

        .pe-5 {
            padding-right: 3rem !important;
        }

        .pb-0 {
            padding-bottom: 0 !important;
        }

        .pb-1 {
            padding-bottom: 0.25rem !important;
        }

        .pb-2 {
            padding-bottom: 0.5rem !important;
        }

        .pb-3 {
            padding-bottom: 1rem !important;
        }

        .pb-4 {
            padding-bottom: 1.5rem !important;
        }

        .pb-5 {
            padding-bottom: 3rem !important;
        }

        .ps-0 {
            padding-left: 0 !important;
        }

        .ps-1 {
            padding-left: 0.25rem !important;
        }

        .ps-2 {
            padding-left: 0.5rem !important;
        }

        .ps-3 {
            padding-left: 1rem !important;
        }

        .ps-4 {
            padding-left: 1.5rem !important;
        }

        .ps-5 {
            padding-left: 3rem !important;
        }

        .text-start {
            text-align: left !important;
        }

        .text-end {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-wrap {
            white-space: normal !important;
        }

        .text-nowrap {
            white-space: nowrap !important;
        }
    </style>
</head>

<body>
    @php
        $companyInformation = gs('company_information');
    @endphp

    <div class="invoice invoice-wrapper">
        <div class="invoice-header clearfix">
            <table class="w-100">
                <tbody>
                    <tr>
                        <td class="w-50 align-middle">
                            <img class="invoice-logo mt-4"
                                src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(siteLogo())) }}" />
                        </td>
                        <td class="w-50 align-middle">
                            <ul class="invoice-company-info float-end">
                                <li class="invoice-company-info__item">
                                    <span class="label">@lang('Address'):</span>
                                    {{ __(@$companyInformation->address) }}
                                </li>
                                <li class="invoice-company-info__item">
                                    <span class="label">@lang('Email'):</span>
                                    {{ @$companyInformation->email ?? __('N/A') }}
                                </li>
                                <li class="invoice-company-info__item">
                                    <span class="label">@lang('Phone'):</span>
                                    {{ @$companyInformation->phone ?? __('N/A') }}
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="invoice-body">
            <table class="w-100">
                <tbody>
                    <tr>
                        <td class="w-25 align-middle">
                            <ul class="invoice-info clearfix">
                                <li class="invoice-info__item float-start me-5">
                                    <span class="label">@lang('Transfer Date')</span>
                                    <p class="value">
                                        {{ showDateTime($transfer->transfer_date, 'F d, Y') }}
                                        <br>
                                        <span>{{ showDateTime($transfer->created_at, 'h:m A') }}</span>
                                    </p>
                                </li>
                                <li class="invoice-info__item float-end">
                                    <span class="label">@lang('Invoice Number')</span>
                                    <p class="value me-4">
                                        {{ __($transfer->invoice_number) }}
                                    </p>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="invoice-pdt">
                <div class="invoice-pdt__body">
                    <div class="invoice-pdt-table-wrapper">
                        <table class="invoice-pdt-table w-100">
                            <thead>
                                <tr>
                                    <th class="text-start">@lang('Product')</th>
                                <th class="text-end">@lang('Qty')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfer->stockTransferDetails  as $transferDetail)
                                    <tr>
                                        <td class="text-start text-nowrap">
                                            {{ strLimit(__(@$transferDetail->product->name), 10) }}
                                            <span> - {{ @$transferDetail->productDetail->sku }}</span>
                                            @if (@$transferDetail->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                                                <span>
                                                    - {{ __(@$transferDetail->productDetail->attribute->name) }}
                                                    - {{ __(@$transferDetail->productDetail->variant->name) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ __($transferDetail->quantity) }}
                                        </td>
                                    </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="invoice-pdt__footer">
                    <table class="w-100 mt-3">
                        <tbody>
                            <tr>
                                <td class="w-50">
                                    <div class="invoice-payment float-start">
                                        <h6 class="invoice-payment__title mb-2">@lang('From Warehouse')</h6>
                                        <ul class="invoice-customer-info">
                                            <li class="invoice-customer-info__item"><span class="label">@lang('Name'):</span>
                                                <span class="value">{{ __(@$transfer->fromWarehouse->name) }}</span>
                                            </li>
                                            <li class="invoice-customer-info__item"><span class="label">@lang('Mobile'):</span>
                                                <span class="value">{{ @$transfer->fromWarehouse->contact_number ?? 'N/A' }}</span>
                                            </li>
                                            <li class="invoice-customer-info__item"><span class="label">@lang('Address'):</span>
                                                <span class="value">{{ __(@$transfer->fromWarehouse->address ?? 'N/A') }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="w-50">
                                    <div class="invoice-payment float-end">
                                        <h6 class="invoice-payment__title mb-2">@lang('To Warehouse')</h6>
                                        <ul class="invoice-customer-info">
                                            <li class="invoice-customer-info__item"><span class="label">@lang('Name'):</span>
                                                <span class="value">{{ __(@$transfer->toWarehouse->name) }}</span>
                                            </li>
                                            <li class="invoice-customer-info__item"><span class="label">@lang('Mobile'):</span>
                                                <span class="value">{{ @$transfer->toWarehouse->contact_number ?? 'N/A' }}</span>
                                            </li>
                                            <li class="invoice-customer-info__item"><span class="label">@lang('Address'):</span>
                                                <span class="value">{{ __(@$transfer->toWarehouse->address ?? 'N/A') }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
