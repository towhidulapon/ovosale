@php
    $cartPayment = (clone $paymentTypes)->where('slug', 'card')->first();
    $cashPayment = (clone $paymentTypes)->where('slug', 'cash')->first();
@endphp

<footer class="pos-footer">
    <div class="pos-footer-left">
        <div class="pos-action-btns">
            <button class="pos-btn pos-btn--primary payment-btn" type="button" data-id="{{ $cashPayment->id }}"
                data-payment-accounts='@json($cashPayment->paymentAccounts)'>
                <x-user.svg.cash />
                <span>{{ __(@$cashPayment->name) }}</span>
            </button>
            <button class="pos-btn pos-btn--info payment-btn" type="button" data-id="{{ $cartPayment->id }}"
                data-payment-accounts='@json($cartPayment->paymentAccounts)'>
                <x-user.svg.card />
                <span>{{ __(@$cartPayment->name) }}</span>
            </button>
            <button class="pos-btn pos-btn--primary multiple-pay-btn" type="button">
                <x-user.svg.multi_pay />
                <span>@lang('Multiple Pay')</span>
            </button>
            <button class="pos-btn pos-btn--danger cancelBtn" type="button">
                <x-user.svg.times />
                <span>@lang('Cancel')</span>
            </button>
        </div>
    </div>
    <div class="pos-footer-right">
        <h1 class="pos-total-amount">
            @lang('Total Payable'): <span>
                {{ gs('cur_sym') }}<span class="total-amount">@lang('0.00')</span>
            </span>
        </h1>
    </div>
</footer>

<x-user.ui.modal id="payment-modal" class="modal-xl">
    <x-user.ui.modal.header>
        <h4 class="modal-title">@lang('Payment')</h4>
        <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
            <i class="las la-times"></i>
        </button>
    </x-user.ui.modal.header>
    <x-user.ui.modal.body>
        <div class="row gy-4">
            <div class="col-lg-7">
                <div class="mb-3 payment-row-wrapper">
                    <div class="single-payment-row">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="form-label">@lang('Paying Amount')</label>
                                <input type="number" step="any" class="form-control paying-amount"
                                    name="payment[0][amount]" form="pos-form" required>
                            </div>
                            <div class="form-group col-lg-6 show-on-multi-payment">
                                <label class="form-label">@lang('Payment Type')</label>
                                <select class="form-control select2 sale-payment-type" name="payment[0][payment_type]"
                                    data-minimum-results-for-search="-1" form="pos-form" required>
                                    <option value="" selected disabled>@lang('Select One')</option>
                                    @foreach ($paymentTypes as $paymentType)
                                        <option value="{{ @$paymentType->id }}"
                                            data-payment-account='@json($paymentType->paymentAccounts)'>
                                            {{ __(@$paymentType->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-6 first-payment-account">
                                <label class="form-label">@lang('Payment Account')</label>
                                <select name="payment[0][payment_account_id]"
                                    class="form-control select2 payment-account" form="pos-form" required>
                                    <option value="" selected disabled>
                                        @lang('Select Payment Type')
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Payment Note')</label>
                            <textarea name="payment[0][note]" class="form-control" form="pos-form"></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group show-on-multi-payment">
                    <button type="submit" class="btn btn--primary btn-large add-payment-row">
                        <i class="fa fa-plus"></i> @lang('Add Payment Row')
                    </button>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex gap-2 flex-column mb-4">
                    <div class="d-flex gap-2">
                        <div class="bg--info rounded p-3 flex-fill w-50">
                            <div class="text-center">
                                <h3 class="mb-0 text-white">
                                    <span class="total-items"></span>
                                </h3>
                                <p class="text-white fs-16">@lang('Total Items')</p>
                            </div>
                        </div>
                        <div class="bg--warning rounded p-3 flex-fill w-50">
                            <div class="text-center">
                                <h3 class="mb-0 text-white">{{ gs('cur_sym') }}<span class="payment-total"></span></h3>
                                <p class="text-white fs-16">@lang('Total Amount')</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="bg--info rounded p-3 flex-fill w-50">
                            <div class="text-center">
                                <h3 class="mb-0 text-white">
                                    {{ gs('cur_sym') }}<span class="payment-discount"></span>
                                </h3>
                                <p class="text-white fs-16">@lang('Discount')</p>
                            </div>
                        </div>
                        <div class="bg--warning rounded p-3 flex-fill w-50">
                            <div class="text-center">
                                <h3 class="mb-0 text-white">
                                    {{ gs('cur_sym') }}<span class="payment-payable"></span>
                                </h3>
                                <p class="text-white fs-16">@lang('Total Payable')</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="bg--info rounded p-3 flex-fill w-50">
                            <div class="text-center">
                                <h3 class="mb-0 text-white">
                                    {{ gs('cur_sym') }}<span class="payment-paying">@lang('0.00')</span>
                                </h3>
                                <p class="text-white fs-16">@lang('Total Paying')</p>
                            </div>
                        </div>
                        <div class="bg--danger rounded p-3 flex-fill w-50">
                            <div class="text-center">
                                <h2 class="mb-0 text-white">
                                    {{ gs('cur_sym') }}<span class="payment-changes">@lang('0.00')</span>
                                </h2>
                                <p class="text-white fs-16">@lang('Changes Amount')</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4 changes-return-payment-wrapper d-none">
                    <label class="form-label">@lang('Changes / Return Form')</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="flex-fill">
                            <select class="form-control select2 changes-payment-type" name="change_payment_type"
                                data-minimum-results-for-search="-1" form="pos-form" form="pos-form">
                                <option value="" selected disabled>@lang('Select One')</option>
                                @foreach ($paymentTypes as $paymentType)
                                    <option value="{{ @$paymentType->id }}"
                                        data-payment-account='@json($paymentType->paymentAccounts)'>
                                        {{ __(@$paymentType->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-fill">
                            <select class="form-control select2 changes-payment-account" name="change_payment_account"
                                data-minimum-results-for-search="-1" form="pos-form" form="pos-form">
                                <option value="" selected disabled>@lang('Select One')</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-center mb-2">
                    <button type="button" class="btn btn--secondary btn-large" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> @lang('Close')
                    </button>
                    <button type="button" class="btn btn--success btn-large only-save" form="pos-form">
                        <i class="fa-regular fa-paper-plane"></i> @lang('Save')
                    </button>
                    <button type="submit" class="btn btn--primary btn-large" form="pos-form">
                        <i class="fa fa-print"></i> @lang('Save & Print')
                    </button>
                </div>
            </div>
        </div>
    </x-user.ui.modal.body>
</x-user.ui.modal>

@push('script')
    <script>
        "use strict";
        (function($) {

            const $paymentModal = $("#payment-modal");
            const $paymentRowWrapper = $paymentModal.find('.payment-row-wrapper');
            let countPaymentRow = 1;
            let saveActionType = 'save_and_print';

            //form submit handler
            $("#pos-form").on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData($(this)[0]);
                formData.append('save_action_type', saveActionType);
                formData.append('status', "{{ Status::SALE_FINAL }}");
                const $this = $(this);

                $.ajax({
                    url: "{{ route('user.sale.store') }}",
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.pos-loader').removeClass('d-none');
                    },
                    complete: function() {
                        setTimeout(() => {
                            $('.pos-loader').addClass('d-none');
                        }, 500);
                    },
                    success: function(resp) {
                        if (resp.status == 'success') {
                            $('#customer-select2')
                                .find('select')
                                .html(`<option value="1">{{ __('Walk-in Customer') }}</option>`);

                            $paymentModal.modal('hide');
                            $paymentModal.find('.show-on-multi-payment').addClass('d-none');
                            $paymentModal.find('form').trigger('reset');
                            $paymentRowWrapper.find('.single-payment-row:not(:first-child)')
                            .remove();
                            functionManager.resetCart();

                            if (saveActionType == 'save_and_print') {
                                $('body').append(
                                    `<div class="print-content">${resp.data.html}</div>`
                                );
                            }
                            setTimeout(() => {
                                if (saveActionType == 'save_and_print') {
                                    window.print();
                                } else {
                                    saveActionType = "save_and_print";
                                    notify('success', resp.message);
                                }
                            }, 800);
                        } else {
                            setTimeout(() => {
                                notify(resp.status, resp.message);
                            }, 500);
                        }

                    }
                });
            });

            $(window).on('afterprint', function(e) {
                saveActionType = "save_and_print";
                $('body').find('.pos-invoice').remove();
            });

            $('.multiple-pay-btn').on('click', function() {
                if (functionManager.cartItemCount() <= 0) {
                    notify("error", "{{ __('Please add item to the cart for the sale') }}");
                    return;
                }
                functionManager.setSummaryData();
                $paymentModal.find('.show-on-multi-payment').removeClass('d-none');
                $('body').find('.first-payment-account').addClass('col-lg-6').removeClass('col-lg-12');
                $paymentModal.modal('show');
            });

            $('.only-save').on('click', function() {
                saveActionType = "only_save";
                $("#pos-form").submit();
            });

            $('.payment-btn').on('click', function() {
                if (functionManager.cartItemCount() <= 0) {
                    notify("error", "{{ __('Please add item to the cart for the sale') }}");
                    return;
                }

                const id = $(this).data('id');
                $('body').find(".paying-amount").val(0);

                if (id && id != undefined) {
                    $('body').find(".sale-payment-type").val(id);
                } else {
                    $('body').find(".sale-payment-type").val(0);
                }

                const accounts = $(this).data('payment-accounts');
                let html = ``;

                if (accounts && accounts.length > 0) {
                    accounts.forEach(account => {
                        html += `<option value="${account.id}">
                            ${account.account_name} - ${account.account_number}
                        </option>`
                    });
                } else {
                    html += `<option selected disabled value="">@lang('Select Payment Account')</option>`
                }

                $('body').find('.payment-account').html(html).trigger('change');
                $('body').find('.first-payment-account').removeClass('col-lg-6').addClass('col-lg-12');

                functionManager.setSummaryData();
                $paymentModal.find('.show-on-multi-payment').addClass('d-none');
                $paymentRowWrapper.find('.single-payment-row:not(:first-child)').remove();
                $paymentModal.modal('show');
            });

            $('body').on('click', '.payment-remove-btn', function() {
                $(this).closest('.single-payment-row').remove();
                functionManager.calculateChangeAmount();
            });

            $('body').on('input', '.paying-amount', function() {
                functionManager.calculateChangeAmount();
            });

            $('.add-payment-row').on('click', function() {
                $paymentRowWrapper.append(htmlManager.paymentRowHtml)
                countPaymentRow++;
                functionManager.select2ReInit();
            });

            $('.cancelBtn').on('click', function() {
                if (functionManager.cartItemCount() <= 0) {
                    notify("error", "{{ __('The cart is empty') }}");
                    return;
                }
                if (confirm("{{ __('Are you sure to clear the cart?') }}")) {
                    functionManager.resetCart();
                    window.calculateAll();
                    $('body').find('.cart-count').text(0);
                }
            });

            $('body').on('change', '.sale-payment-type', function(e) {

                const accounts = $(this).find('option:selected').data('payment-account');
                let html = `<option selected disabled value="">{{ __('Select Payment Type') }}</option>`;

                if (accounts && accounts.length > 0) {
                    accounts.forEach(account => {
                        html += `<option value="${account.id}">
                            ${account.account_name} - ${account.account_number}
                        </option>`
                    });
                } else {
                    html = `<option selected disabled value="">{{ __('Select Payment Type') }}</option>`;
                }
                $(this).closest('.single-payment-row').find('.payment-account').html(html).trigger('change');
            });


            $('body').on('change', '.changes-payment-type', function(e) {
                const accounts = $(this).find('option:selected').data('payment-account');
                let html = `<option selected disabled value="">{{ __('Select Payment Type') }}</option>`;
                if (accounts && accounts.length > 0) {
                    accounts.forEach(account => {
                        html += `<option value="${account.id}">
                            ${account.account_name} - ${account.account_number}
                        </option>`
                    });
                } else {
                    html = `<option selected disabled value="">{{ __('Select Payment Type') }}</option>`;
                }
                $('body').find('.changes-payment-account').html(html).trigger('change');
            });

            const functionManager = {
                /**
                 * Resets the cart by clearing the cart items, resetting the totals, and updating the UI to reflect an empty cart state.
                 *
                 * @returns {void} - No return value.
                 */
                resetCart: function() {
                    $('.pos-cart-table__tbody').html(htmlManager.cartEmptyHtml);
                    $('body').find('.summary-discount').text('0.00');
                    $('body').find('.pos-total-amount .total-amount').text('0.00');
                    $('body').find('.pos-cart-summery-list .summary-subtotal').text('0.00');
                    $('body').find('.pos-sidebar-body').addClass('product-cart-empty');
                    window.added_product_id = [];
                },

                /**
                 * Calculates the change amount based on the total payable amount and the amount being paid, then updates the payment modal with the calculated values.
                 *
                 * @returns {void} - No return value.
                 */
                calculateChangeAmount: function() {
                    const totalPayAmount = parseFloat($('.pos-total-amount').find('.total-amount').text());
                    let payingAmount = 0;

                    $.each($paymentRowWrapper.find('.paying-amount'), function(i, element) {
                        const singlePayingAmount = parseFloat($(element).val() || 0);
                        payingAmount += singlePayingAmount;
                    });

                    const changeAmount = payingAmount > 0 ? payingAmount - totalPayAmount : payingAmount;
                    $paymentModal.find('.payment-changes').text(showAmount(changeAmount));

                    if (changeAmount > 0 && $paymentModal.find('.single-payment-row').length >= 2) {
                        $(".changes-return-payment-wrapper").removeClass('d-none');
                    } else {
                        $(".changes-return-payment-wrapper").addClass('d-none');
                    }
                    $paymentModal.find('.payment-paying').text(showAmount(payingAmount));
                },

                /**
                 * Sets the summary data in the payment modal, including item count, total, discount, and payable amount, and triggers the change amount calculation.
                 *
                 * @returns {void} - No return value.
                 */
                setSummaryData: function() {
                    const totalPayAmount = $('.pos-total-amount').find('.total-amount').text();
                    $paymentModal.find('.total-items').text(functionManager.cartItemCount());
                    $paymentModal.find('.payment-total').text($('.pos-sidebar-footer')
                        .find('.summary-subtotal')
                        .text());
                    $paymentModal.find('.payment-discount').text($('.pos-sidebar-footer')
                        .find('.summary-discount')
                        .text());
                    $paymentModal.find('.payment-payable').text(totalPayAmount);
                    functionManager.calculateChangeAmount();
                },

                /**
                 * Counts the number of items currently in the cart by checking the number of cart item elements.
                 *
                 * @returns {number} - The total number of items in the cart.
                 */
                cartItemCount: function() {
                    return $(".pos-cart-table__tbody").find('.card-single-item').length;
                },

                /**
                 * Reinitializes all select2 dropdowns with specific options to ensure proper functionality and layout.
                 *
                 * @returns {void} - No return value.
                 */
                select2ReInit: function() {
                    $.each($('.select2'), function() {
                        $(this)
                            .wrap(`<div class="position-relative"></div>`)
                            .select2({
                                dropdownParent: $(this).parent(),
                                width: "100%",
                            });
                    });
                }

            }

            /**
             * Contains HTML templates used for rendering different UI elements in the POS system.
             * Each method returns a string of HTML that represents a specific UI component.
             */
            const htmlManager = {

                /**
                 * Generates the HTML structure for an empty cart message.
                 *
                 * @returns {string} - The HTML structure for the empty cart message.
                 */
                cartEmptyHtml: function() {
                    return `
                        <div class="product-empty-message">
                            <div class="p-5 text-center">
                                <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                                <span class="d-block">@lang('Cancel')</span>
                                <span class="d-block fs-13 text-muted">@lang('Total Payable')</span>
                            </div>
                        </div>
                    `;
                },

                /**
                 * Generates the HTML structure for a payment row, including fields for the paying amount, payment type, and payment note.
                 *
                 * @returns {string} - The HTML structure for a payment row.
                 */
                paymentRowHtml: function() {
                    return `
                        <div class="single-payment-row">
                            <hr/>
                            <hr/>
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="form-label">@lang('0.00')</label>
                                    <input type="number" class="form-control paying-amount" name="payment[${countPaymentRow}][amount]" form="pos-form" required>
                                </div>
                                <div class="form-group col-lg-6 show-on-multi-payment">
                                    <label class="form-label">@lang('Payment')</label>
                                    <select class="form-control select2 sale-payment-type" name="payment[${countPaymentRow}][payment_type]"
                                        data-minimum-results-for-search="-1" form="pos-form" required>
                                        <option value="" selected disabled>@lang('Paying Amount')</option>
                                        @foreach ($paymentTypes as $paymentType)
                                            <option value="{{ @$paymentType->id }}" data-payment-account='@json($paymentType->paymentAccounts)'>
                                                {{ __(@$paymentType->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="form-label">@lang('Payment Type')</label>
                                    <select name="payment[${countPaymentRow}][payment_account_id]"
                                        class="form-control select2 payment-account" required form="pos-form">
                                        <option value="" selected disabled>@lang('Select One')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Payment Account')</label>
                                <div class="row align-items-center">
                                    <div class="col-lg-10">
                                        <textarea name="payment[${countPaymentRow}][note]" class="form-control" form="pos-form"></textarea>
                                    </div>
                                    <div class="col-lg-2">
                                        <button class="btn btn--danger btn-large payment-remove-btn" type="button"><i class="las la-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        #payment-modal textarea.form--control,
        textarea.form-control {
            height: 70px;
        }

        .payment-remove-btn {
            padding: 21px 29px !important;
        }
    </style>
@endpush
