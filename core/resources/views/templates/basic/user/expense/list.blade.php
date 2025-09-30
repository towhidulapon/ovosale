@extends($activeTemplate . 'layouts.master')
@section('panel')
@include('Template::user.expense.widget')
<div class="row">
    <div class="col-12">
        <x-user.ui.card>
            <x-user.ui.card.body :paddingZero=true>
                <x-user.ui.table.layout filterBoxLocation="expense.filter_form">
                    <x-user.ui.table>
                        <x-user.ui.table.header>
                            <tr>
                                <th>@lang('Date') | @lang('Purpose')</th>
                                <th>@lang('Expense From Account')</th>
                                <th>@lang('Reference No')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Added By')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </x-user.ui.table.header>
                        <x-user.ui.table.body>
                            @forelse($expenses as $expense)
                            <tr>
                                <td>
                                    <div class="text-start">
                                        <span class="d-block">{{ showDateTime($expense->expense_date, 'Y-m-d') }}</span>
                                        <span class="d-block fs-12">{{ __(@$expense->category->name) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <span class="d-block">
                                            {{ __(@$expense->paymentAccount->account_name) }} - {{
        __(@$expense->paymentAccount->account_number) }}
                                        </span>
                                        <span class="d-block fs-12">{{ __(@$expense->paymentType->name) }}</span>
                                    </div>
                                </td>
                                <td>{{ __(@$expense->reference_no ?? 'N/A') }}</td>
                                <td>{{ showAmount(@$expense->amount) }}</td>
                                <td>{{ __(@$expense->admin->name) }}</td>
                                <td>
                                    <x-user.ui.btn.table_action module="expense" :id="$expense->id">
                                        <x-permission_check permission="edit expense">
                                            <x-user.ui.btn.edit data-expense="{{ $expense }}" tag="btn" />
                                        </x-permission_check>
                                        @if (@$expense->attachment)
                                        <a href="{{ route('user.download.attachment', encrypt(getFilePath('expense_attachment') . '/' . @$expense->attachment)) }}"
                                            class="btn btn--success">
                                            <i class="las la-download text--success"></i>
                                            @lang('Attachment')
                                        </a>
                                        @endif
                                    </x-user.ui.btn.table_action>
                                </td>
                            </tr>
                            @empty
                            <x-user.ui.table.empty_message />
                            @endforelse
                        </x-user.ui.table.body>
                    </x-user.ui.table>
                    @if ($expenses->hasPages())
                    <x-user.ui.table.footer>
                        {{ paginateLinks($expenses) }}
                    </x-user.ui.table.footer>
                    @endif
                </x-user.ui.table.layout>
            </x-user.ui.card.body>
        </x-user.ui.card>
    </div>
</div>

<x-user.ui.modal id="modal">
    <x-user.ui.modal.header>
        <h4 class="modal-title">@lang('Add Expense')</h4>
        <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
            <i class="las la-times"></i>
        </button>
    </x-user.ui.modal.header>
    <x-user.ui.modal.body>
        <form method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="form-group col-lg-12">
                    <label>@lang('Expense Date')</label>
                    <input type="text" class="form-control date-picker" name="expense_date" required
                        value="{{ old('expense_date') }}">
                </div>
                <div class="form-group col-lg-6">
                    <label>@lang('Expense Purpose')</label>
                    <select name="expense_purpose" class="form-control select2" required>
                        <option value="" selected disabled>@lang('Select Purpose')</option>
                        @foreach ($expenseCategories as $expenseCategory)
                        <option value="{{ @$expenseCategory->id }}">{{ __(@$expenseCategory->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label>@lang('Amount')</label>
                    <div class="input-group input--group">
                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                        <input type="number" step="any" class="form-control" name="amount" required
                            value="{{ old('amount') }}">
                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label>@lang('Payment Type')</label>
                    <select name="payment_type" class="form-control select2 payment-type">
                        <option value="" selected disabled>@lang('Select Option')</option>
                        @foreach ($paymentMethods as $paymentMethod)
                        <option value="{{ $paymentMethod->id }}"
                            data-payment-account='@json($paymentMethod->paymentAccounts)'>
                            {{ __($paymentMethod->name) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label>@lang('Payment Account')</label>
                    <select name="payment_account" class="form-control select2 payment-account" required>
                        <option value="" selected disabled>@lang('Select Payment Type')</option>
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label>@lang('Reference No')</label>
                    <input type="text" class="form-control" name="reference_no" value="{{ old('reference_no') }}">
                </div>
                <div class="form-group col-lg-6">
                    <label>@lang('Attachment')</label>
                    <input type="file" class="form-control" name="attachment">
                </div>
                <div class="form-group col-lg-12">
                    <label>@lang('Comment')</label>
                    <textarea name="comment" class="form-control">{{ old('comment') }}</textarea>
                </div>
                <div class="form-group col-lg-12">
                    <x-user.ui.btn.modal />
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
                const action = "{{ route('user.expense.create') }}";

                $modal.find('.modal-title').text("@lang('Add Expense')");
                $form.trigger('reset');

                $form.attr('action', action);
                $modal.find('[name=payment_type]').attr('disabled',false).trigger('change');
                $modal.find('[name=payment_account]').attr('disabled',false).trigger('change');

                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.expense.update', ':id') }}";
                const expense = $(this).data('expense');

                $modal.find('.modal-title').text("@lang('Date')");
                $modal.find('input[name=name]').val(expense.name);
                $modal.find('input[name=expense_date]').val(expense.expense_date);
                $modal.find('input[name=reference_no]').val(expense.reference_no);
                $modal.find('textarea[name=comment]').val(expense.comment);
                $modal.find('input[name=amount]').val(getAmount(expense.amount));
                $modal.find('[name=expense_purpose]').val(expense.category_id).trigger('change');
                $modal.find('[name=payment_type]').val(expense.payment_type_id).attr('disabled',true).trigger('change');
                $modal.find('[name=payment_account]').val(expense.payment_account_id).attr('disabled',true).trigger('change');
                $form.attr('action', action.replace(':id', expense.id));
                $modal.modal('show');
            });

            $(".date-picker").flatpickr({
                maxDate: new Date(),
            });


            @if (@request()->popup == 'yes')
                setTimeout(() => {
                    $('.add-btn').trigger('click');
                }, 500);
            @endif

            $('.payment-type').on('change',function(e){
                const accounts = $(this).find('option:selected').data('payment-account');
                let   html     = ``;

                if(accounts && accounts.length > 0){
                    accounts.forEach(account => {
                        html+=`<option value="${account.id}">
                            ${account.account_name} - ${account.account_number}
                        </option>`
                    });
                }else{
                    html+=`<option selected disabled value="">@lang('No Account Found')</option>`
                }
                $('.payment-account').html(html).trigger('change');

            });
        })(jQuery);
</script>
@endpush
@push('breadcrumb-plugins')
<x-user.ui.btn.add tag="btn" />
@endpush


@push('script-lib')
<script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush

@push('style-lib')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush