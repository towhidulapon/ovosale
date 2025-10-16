@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card>
                <x-user.ui.card.body :paddingZero=true>
                    <x-user.ui.table.layout :hasRecycleBin="false">
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Employee')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Payment Account')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
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
                                            <x-staff_permission_check permission="edit payroll">
                                                <x-user.ui.btn.edit tag="btn" :data-payroll="$payroll" />
                                            </x-staff_permission_check>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($payrolls->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($payrolls) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add payroll')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-user.ui.modal.header>
        <x-user.ui.modal.body>
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
                            <x-user.ui.btn.modal />
                        </div>
                    </div>
                </div>
            </form>
        </x-user.ui.modal.body>
    </x-user.ui.modal>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            const $modal = $('#modal');
            const $form = $modal.find('form');

            $('.add-btn').on('click', function() {
                const action = "{{ route('user.payroll.create') }}"
                $modal.find('.modal-title').text("@lang('Add Payroll')");
                $form.trigger('reset');
                $modal.find('select[name=employee_id]').trigger('change');
                $modal.find('select[name=payment_method_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.payroll.update', ':id') }}";
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
    <x-staff_permission_check permission="add payroll">
        <x-user.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
