@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('Template::user.purchase.widget')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout  :hasRecycleBin="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Invoice Number') | @lang('Reference')</th>
                                    <th>@lang('Purchase Date') | @lang('Created At')</th>
                                    <th>@lang('Warehouse') | @lang('Supplier')</th>
                                    <th>@lang('Total Amount') | @lang('Paid Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Add By')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($purchases as $purchase)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __($purchase->invoice_number) }}</span>
                                                <span>{{ __($purchase->reference_number ?? 'N/A') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span
                                                    class="d-block">{{ showDateTime($purchase->purchase_date, 'Y-m-d') }}</span>
                                                <span>{{ showDateTime($purchase->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __(@$purchase->warehouse->name) }}</span>
                                                <span>{{ __(@$purchase->supplier->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($purchase->total) }}</span>
                                                <span
                                                    class="text--success">{{ showAmount($purchase->supplier_payments_sum_amount) }}</span>
                                            </div>
                                        </td>
                                        <td> @php echo $purchase->statusBadge @endphp </td>
                                        <td> {{ __(@$purchase->admin->username) }} </td>
                                        <td class="dropdown">
                                            @if (request()->trash)
                                                <button type="button" class="btn btn-outline--success confirmationBtn"
                                                    data-question='@lang('Are you sure to restore this purchase?')'
                                                    data-action="{{ route('user.purchase.trash.restore', $purchase->id) }}">
                                                    <i class="las la-undo"></i>
                                                    @lang('Restore')
                                                </button>
                                            @else
                                                <button class=" btn btn-outline--primary" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    @lang('Action') <i class="las la-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown">
                                                    <x-permission_check permission="edit purchase">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('user.purchase.edit', $purchase->id) }}">
                                                            <span class="me-2">
                                                                <i class="las la-pencil-alt text--primary"></i>
                                                            </span>
                                                            @lang('Edit Purchase')
                                                        </a>
                                                    </x-permission_check>
                                                    <x-permission_check permission="view purchase">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('user.purchase.view', $purchase->id) }}">
                                                            <span class="me-2">
                                                                <i class="las la-eye text--success"></i>
                                                            </span>
                                                            @lang('View Invoice')
                                                        </a>
                                                    </x-permission_check>
                                                    <x-permission_check permission="print purchase invoice">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start print-btn"
                                                            target="_blank"
                                                            data-action="{{ route('user.purchase.print', $purchase->id) }}">
                                                            <span class="me-2">
                                                                <i class="las la-print text--dark"></i>
                                                            </span>
                                                            @lang('Print Invoice')
                                                        </button>
                                                    </x-permission_check>
                                                    <x-permission_check permission="download purchase invoice">
                                                        <a class="dropdown-list d-block w-100 text-start"
                                                            href="{{ route('user.purchase.pdf', $purchase->id) }}">
                                                            <span class="me-2">
                                                                <i class="las  la-file-download text--info"></i>
                                                            </span>
                                                            @lang('Download Invoice')
                                                        </a>
                                                    </x-permission_check>
                                                    <x-permission_check permission="update purchase status">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start update-status"
                                                            data-id="{{ $purchase->id }}" @disabled($purchase->status == Status::PURCHASE_RECEIVED)>
                                                            <span class="me-2">
                                                                <i class="las la-edit text--success"></i>
                                                            </span>
                                                            @lang('Update Status')
                                                        </button>
                                                    </x-permission_check>
                                                    <x-permission_check permission="add purchase payment">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start add-payment"
                                                            data-purchase='@json($purchase)'
                                                            @disabled($purchase->total <= $purchase->supplier_payments_sum_amount)>
                                                            <span class="me-2">
                                                                <i class="las la-plus-circle text--primary"></i>
                                                            </span>
                                                            @lang('Add Payment')
                                                        </button>
                                                    </x-permission_check>
                                                    <x-permission_check permission="view purchase payment">
                                                        <button type="button"
                                                            class="dropdown-list d-block w-100 text-start payment-history"
                                                            data-purchase='@json($purchase)'>
                                                            <span class="me-2">
                                                                <i class="las la-list text--success"></i>
                                                            </span>
                                                            @lang('Payment History')
                                                        </button>
                                                    </x-permission_check>

                                                    <x-permission_check permission="view purchase">
                                                        @if ($purchase->attachment)
                                                            <a class="dropdown-list d-block w-100 text-start"
                                                                href="{{ route('admin.download.attachment', encrypt(getFilePath('purchase_attachment') . '/' . $purchase->attachment)) }}">
                                                                <span class="me-2">
                                                                    <i class="las la-download text--warning"></i>
                                                                </span>
                                                                @lang('Attachment')
                                                            </a>
                                                        @else
                                                            <a class="dropdown-list d-block w-100 text-start"
                                                                href="javascript:void(0)">
                                                                <span class="me-2">
                                                                    <i class="las la-download text--warning"></i>
                                                                </span>
                                                                @lang('Attachment')
                                                            </a>
                                                        @endif
                                                    </x-permission_check>

                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($purchases->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($purchases) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="payment-modal" class="modal-xl">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Payment')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="" method="POST">
                @csrf
                <div class="row gy-4">
                    <div class="col-12">
                        <div class="row purchase-supplier-info"></div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>@lang('Paid Amount')</label>
                                <div class="input-group input--group">
                                    <input type="number" step="any" max="0" class="form-control"
                                        name="paid_amount" placeholder="@lang('0.00')" required>
                                    <span class="input-group-text">
                                        {{ __(gs('cur_text')) }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>@lang('Paid Date')</label>
                                <div class="input-group input--group">
                                    <input type="text" class="form-control date-picker-here" name="paid_date"
                                        value="{{ date('Y-m-d') }}" required>
                                    <span class="input-group-text">
                                        <i class="las la-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>@lang('Payment Method')</label>
                                <select name="payment_type" class="form-control select2 payment-type" required>
                                    <option value="" selected disabled>@lang('Select Option')</option>
                                    @foreach ($paymentMethods as $paymentMethod)
                                        <option value="{{ $paymentMethod->id }}" data-payment-account='@json($paymentMethod->paymentAccounts)'>
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
                            <div class="form-group col-lg-12 mb-0">
                                <label>@lang('Payment Note')</label>
                                <textarea class="form-control" name="payment_note"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <x-admin.ui.btn.modal />
                        </div>
                    </div>
                </div>
            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>


    <x-admin.ui.modal id="payment-history-modal" class="modal-xl">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Payment History')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>

        </x-admin.ui.modal.body>
    </x-admin.ui.modal>

    <x-confirmation-modal />

    <x-admin.ui.modal id="status-modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Payment History')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label>@lang('Status')</label>
                    <select class="form-control select2" data-minimum-results-for-search="-1" required name="status">
                        <option value="" selected disabled>@lang('Select One')</option>
                        <option value="{{ Status::PURCHASE_PENDING }}">@lang('Pending')</option>
                        <option value="{{ Status::PURCHASE_RECEIVED }}">@lang('Received')</option>
                        <option value="{{ Status::PURCHASE_ORDERED }}">@lang('Ordered')</option>
                    </select>
                </div>
                <div class="form-group">
                    <x-admin.ui.btn.modal />
                </div>
            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>
@endsection

@push('breadcrumb-plugins')
    <x-permission_check permission="add purchase">
        <x-admin.ui.btn.add href="{{ route('user.purchase.add') }}" text="Add Purchase" />
    </x-permission_check>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            const $paymentModal = $('#payment-modal');
            const $paymentHistoryModal = $('#payment-history-modal');
            const $statusModal = $('#status-modal');

            $('.add-payment').on('click', function() {
                const purchase = $(this).data('purchase');
                const duyAmount = getAmount(getAmount(purchase.total) - getAmount(purchase
                    .supplier_payments_sum_amount || 0));
                const action = ("{{ route('user.purchase.ad.payment', ':id') }}").replace(":id", purchase.id);

                $paymentModal.find('.purchase-supplier-info').html(`
                    <div class="row justify-content-between">
                        <div class="col-lg-4">
                            <h6 class="mb-2">@lang('Purchase Information')</h6>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Invoice'): </span>
                                <span> ${purchase.invoice_number}</span>
                            </span>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Reference'): </span>
                                <span>${purchase.reference_number || 'N/A'}</span>
                            </span>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Purchase Date'): </span>
                                <span class="purchase-date">${purchase.purchase_date}</span>
                            </span>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Total Amount'): </span>
                                <span class="purchase-total">
                                    {{ gs('cur_sym') }}${getAmount(purchase.total)}
                                </span>
                            </span>
                             <span class="d-flex gap-2 flex-wrap justify-content-between">
                                 <span class="text-muted">@lang('Total Paid Amount'):</span>
                                <span>{{ gs('cur_sym') }}${getAmount(purchase.supplier_payments_sum_amount || 0)}</span>
                            </span>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Total Due Amount'): </span>
                                <span class="fw-bold text--danger">{{ gs('cur_sym') }}${duyAmount || 0} </span>
                            </span>
                        </div>
                        <div class="col-lg-6 text-end">
                            <h6 class="mb-2">@lang('Supplier Information')</h6>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Name'): </span>
                                <span class="purchase-supplier-name">${purchase?.supplier?.name || 'N/A'}</span>
                            </span>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Company Name'): </span>
                                <span class="purchase-supplier-name">${purchase?.supplier?.company_name || 'N/A'}</span>
                            </span>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Email'): </span>
                                <span class="purchase-supplier-email">${purchase?.supplier?.email || 'N/A'}</span>
                            </span>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Mobile'): </span>
                                <span class="purchase-supplier-mobile">${purchase?.supplier?.mobile || 'N/A'}</span>
                            </span>
                            <span class="d-flex gap-2 flex-wrap justify-content-between">
                                <span class="text-muted"> @lang('Address'): </span>
                                <span class="purchase-supplier-address">${purchase?.supplier?.address || 'N/A'}</span>
                            </span>

                        </div>
                    </div>
                `);
                $paymentModal.find('form').attr('action', action);
                $paymentModal.find(`[name=paid_amount]`).val(duyAmount).attr('max', duyAmount);
                $paymentModal.modal('show');
            });

            $(".payment-history").on('click', function() {
                const purchase = $(this).data('purchase');
                const duyAmount = getAmount(getAmount(purchase.total) - getAmount(purchase
                    .supplier_payments_sum_amount || 0));

                let html = "";
                if(purchase.supplier_payments && purchase.supplier_payments.length > 0){
                        $.each(purchase.supplier_payments, function(i, payment) {
                        html += `
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between  gap-2 flex-wrap ps-0">
                                    <span class="text-muted">@lang('Date')</span>
                                    <span>${payment.payment_date}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between  gap-2 flex-wrap ps-0">
                                    <span class="text-muted">@lang('Amount')</span>
                                    <span>{{ gs('cur_sym') }}${getAmount(payment.amount)} </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between  gap-2 flex-wrap ps-0">
                                    <span class="text-muted">@lang('Payment Method')</span>
                                    <span>${payment?.payment_type?.name}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between  gap-2 flex-wrap ps-0">
                                    <span class="text-muted">@lang('Payment Note')</span>
                                    <span>${payment?.payment_note || 'N/A'}</span>
                                </li>
                            </ul>
                        ${purchase.supplier_payments.length == (i+1) ? '' : '<hr/><hr/>' }
                        `
                    });
                }else{
                    html += `
                        <h6 class="text-muted">
                           <i> @lang('No payment history found')</i>
                        </h6>
                    `
                }

                $paymentHistoryModal.find('.modal-body').html(`
                    <div class="row gy-4 justify-content-between">
                        <div class="col-lg-4">
                            <h6 class="mb-2">@lang('Supplier Information')</h6>
                            <div class="information">
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Name')</span>
                                    <span>${purchase?.supplier?.name || 'N/A' || 'N/A'}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Email')</span>
                                    <span>${purchase?.supplier?.email || 'N/A' || 'N/A'}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Mobile')</span>
                                    <span>${purchase?.supplier?.mobile || 'N/A' || 'N/A'}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Address')</span>
                                    <span>${purchase?.supplier?.address || 'N/A' || 'N/A'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h6 class="mb-2 text-end">@lang('Purchase Information')</h6>
                            <div class="information">
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Invoice Number')</span>
                                    <span>${purchase?.invoice_number}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Date')</span>
                                    <span>${purchase?.purchase_date}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Total Amount')</span>
                                    <span>{{ gs('cur_sym') }}${showAmount(purchase.total)}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted">@lang('Paid Amount')</span>
                                    <span>{{ gs('cur_sym') }}${showAmount(purchase.supplier_payments_sum_amount || 0)}</span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <span class="text-muted"> @lang('Total Due Amount') </span>
                                    <span class="fw-bold text--danger">{{ gs('cur_sym') }}${duyAmount || 0} </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                             <h6 class="mb-2">@lang('Payment Information')</h6>
                            ${html}
                        </div>
                    </div>
                `);
                $paymentHistoryModal.modal('show');
            });

            $(".date-picker-here").flatpickr({
                maxDate: new Date()
            });

            $(".update-status").on('click', function() {
                const id = $(this).data('id');
                const action = "{{ route('user.purchase.update.status', ':id') }}";
                $statusModal.find('form').attr('action', action.replace(":id", id))
                $statusModal.modal('show');
            });


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
                    html+=`<option selected disabled value="">@lang('No Account F')</option>`
                }
                $('.payment-account').html(html).trigger('change');

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
