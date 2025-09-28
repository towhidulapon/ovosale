@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout  :renderExportButton="false" :hasRecycleBin="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Invoice Number') | @lang('Total Items')</th>
                                    <th>@lang('Transfer Date') | @lang('Created At')</th>
                                    <th>@lang('From Warehouse')</th>
                                    <th>@lang('To Warehouse')</th>
                                    <th>@lang('Add By | Reference')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($transfers as $transfer)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block"><a href="{{ route('admin.stock.transfer.view', $transfer->id) }}">{{ __($transfer->invoice_number) }}</a></span>
                                                <span>{{ @$transfer->total_items }} {{str()->plural('Item', $transfer->total_items)}}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showDateTime($transfer->transfer_date, 'Y-m-d') }}</span>
                                                <span>{{ showDateTime($transfer->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ __(@$transfer->fromWarehouse->name) }}</td>
                                        <td>{{ __(@$transfer->toWarehouse->name) }}</td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __(@$transfer->admin->username) }}</span>
                                                <span>{{ __(@$transfer->reference_no) }}</span>
                                            </div>
                                        </td>
                                        <td class="dropdown">
                                            <button class=" btn btn-outline--primary" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                @lang('Action') <i class="las la-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown">

                                                <x-permission_check permission="edit sale">
                                                    <a class="dropdown-list d-block w-100 text-start"
                                                        href="{{ route('admin.stock.transfer.edit', $transfer->id) }}">
                                                        <span class="me-1">
                                                            <i class="las la-pencil-alt text--primary"></i>
                                                        </span>
                                                        @lang('Edit')
                                                    </a>
                                                </x-permission_check>

                                                <x-permission_check permission="view sale">
                                                    <a class="dropdown-list d-block w-100 text-start"
                                                        href="{{ route('admin.stock.transfer.view', $transfer->id) }}">
                                                        <span class="me-1">
                                                            <i class="las la-eye text--dark"></i>
                                                        </span>
                                                        @lang('View Invoice')
                                                    </a>
                                                </x-permission_check>

                                                <x-permission_check permission="print sale invoice">
                                                    <button type="button"
                                                        class="dropdown-list d-block w-100 text-start print-btn"
                                                        data-action="{{ route('admin.stock.transfer.print', $transfer->id) }}">
                                                        <span class="me-1">
                                                            <i class="las la-print text--success"></i>
                                                        </span>
                                                        @lang('Print Invoice')
                                                    </button>
                                                </x-permission_check>


                                                <x-permission_check permission="download sale invoice">
                                                    <a class="dropdown-list d-block w-100 text-start"
                                                        href="{{ route('admin.stock.transfer.pdf', $transfer->id) }}">
                                                        <span class="me-1">
                                                            <i class="las  la-file-download text--info"></i>
                                                        </span>
                                                        @lang('Download')
                                                    </a>
                                                </x-permission_check>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($transfers->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($transfers) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-permission_check permission="add sale">
        <x-admin.ui.btn.add href="{{ route('admin.stock.transfer.add') }}" text="Add Transfer" />
    </x-permission_check>
@endpush


@push('script')
    <script>
        "use strict";
        (function($) {


            $(".print-btn").on('click', function() {

                const action = $(this).data('action');
                $.ajax({
                    type: "GET",
                    url: action,
                    success: function(response) {
                        if (response.status == 'success') {
                            $('body')
                                .append(`<div class="print-content">${response.data.html}</div>`);
                            window.print();
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $(window).on('afterprint', function() {
                $('body').find('.print-content').remove();
            });

        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .btn-outline--primary i {
            transition: .2s linear;
        }

        .btn-outline--primary.show i {
            transform: rotate(180deg);
        }
    </style>
@endpush



@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/invoice.css') }}">
@endpush
