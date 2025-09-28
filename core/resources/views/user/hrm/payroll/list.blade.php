@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :hasRecycleBin="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Employee')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Payment Account')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($payrolls as $payroll)
                                    <tr>
                                        <td>
                                            <div class="flex-thumb-wrapper">
                                                <div class="thumb">
                                                    <img class="thumb-img" src="{{ $payroll->employee->image_src }}">
                                                </div>
                                                <span class="ms-2">
                                                    {{ __(@$payroll->employee->name) }}<br>
                                                    {{ __(@$payroll->employee->phone) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ showAmount($payroll->amount) }}</td>
                                        <td>{{ __(@$payroll->paymentMethod->name) }}</td>
                                        <td>{{ __(@$payroll->paymentAccount->account_name) }}</td>
                                        <td>{{ showDateTime($payroll->date) }}</td>

                                        <td>
                                            <x-permission_check permission="edit payroll">
                                                <x-admin.ui.btn.edit tag="btn" :data-payroll="$payroll" />
                                            </x-permission_check>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($payrolls->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($payrolls) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add payroll')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label>@lang('Employee')</label>
                        <select class="form-control form--control select2" required name="employee_id">
                            <option value="">@lang('Select Employee')</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ __($employee->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Date')</label>
                        <div class="input-group input--group">
                            <input type="text" class="form-control date-picker-payroll" name="date"
                                value="{{ old('date') }}" required>
                            <span class="input-group-text">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Amount')</label>
                        <input type="number" step="any" class="form-control" name="amount" required
                            value="{{ old('amount') }}">
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Payment Method')</label>
                        <select class="form-control form--control select2 payment-method-select" required name="payment_method_id">
                            <option value="">@lang('Select Payment Method')</option>
                            @foreach ($paymentMethods as $paymentMethod)
                                <option value="{{ $paymentMethod->id }}" @selected(old('payment_method_id') == $paymentMethod->id) data-accounts='@json($paymentMethod->paymentAccounts)'>{{ __($paymentMethod->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>@lang('Payment Account')</label>
                        <select class="form-control form--control select2 account-select" required name="payment_account_id">
                            <option value="">@lang('Please Select The Payment Method')</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <x-admin.ui.btn.modal />
                        </div>
                    </div>
                </div>
            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            const $modal = $('#modal');
            const $form = $modal.find('form');

            $('.add-btn').on('click', function() {
                const action = "{{ route('admin.payroll.create') }}"
                $modal.find('.modal-title').text("@lang('Add Payroll')");
                $form.trigger('reset');
                $modal.find('select[name=employee_id]').trigger('change');
                $modal.find('select[name=payment_method_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('admin.payroll.update', ':id') }}";
                const payroll = $(this).data('payroll');
                $modal.find('.modal-title').text("@lang('Edit Payroll')");
                $modal.find('select[name=employee_id]').val(payroll.employee_id).trigger('change');
                $modal.find('select[name=payment_method_id]').val(payroll.payment_method_id).attr('disabled',true).trigger('change');
                $modal.find('select[name=payment_account_id]').val(payroll.payment_account_id).attr('disabled',true).trigger('change');
                $modal.find('input[name=amount]').val(getAmount(payroll.amount));
                $modal.find('input[name=date]').val(payroll.date);
                $form.attr('action', action.replace(':id', payroll.id));
                $modal.modal('show');
            });

            $(".date-picker-payroll").flatpickr({
                calendar: true,
                maxDate: new Date(),

            });

            $('.payment-method-select').on('change', function() {
                const accounts = $(this).find(`option:selected`).data('accounts');
                let html = `<option selected disabled>@lang('Select One')</option>`;

                if (accounts && accounts.length > 0) {
                    $.each(accounts, function(i, account) {
                        html += `<option value="${account.id}">${account.account_name}</option>`;
                    });
                } else {
                    html = `<option selected disabled>@lang('No Account Found')</option>`;
                }
                $('.account-select').html(html).trigger('change');

            });


        })(jQuery);
    </script>
@endpush


@push('script-lib')
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush

@push('breadcrumb-plugins')
    <x-permission_check permission="add payroll">
        <x-admin.ui.btn.add tag="btn" />
    </x-permission_check>
@endpush
