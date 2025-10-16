@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card>
                <x-user.ui.card.body class="p-0">
                    <x-user.ui.table.layout :renderExportButton="false">
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Coupon')</th>
                                    <th>@lang('Minimum Amount')</th>
                                    <th>@lang('Discount')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Total Used')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block text--success">
                                                    {{ __($coupon->code) }}
                                                </span>
                                                <span class="fs-12">
                                                    {{ __($coupon->name) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ showAmount($coupon->minimum_amount) }}
                                        </td>
                                        <td>
                                            @if ($coupon->discount_type == Status::DISCOUNT_PERCENT)
                                                {{ getAmount($coupon->amount) }}%
                                            @else
                                                {{ showAmount($coupon->amount) }}
                                            @endif
                                        </td>
                                        <td>
                                            <span>
                                                <span class="text--success">
                                                    {{ showDateTime($coupon->start_from, gs('date_format')) }}
                                                </span>
                                                -
                                                <span class="text--warning">
                                                    {{ showDateTime($coupon->end_at, gs('date_format')) }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class=" badge badge--success">
                                                @lang('0 times')
                                            </a>
                                        </td>
                                        <td>
                                            <x-user.other.status_switch :status="$coupon->status" :action="route('user.coupon.status.change', $coupon->id)"
                                                title="coupon" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="coupon" :id="$coupon->id">
                                                <x-staff_permission_check permission="edit coupon">
                                                <x-user.ui.btn.edit tag="button" : :data-resource="$coupon" />
                                                </x-staff_permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($coupons->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($coupons) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title"></h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-user.ui.modal.header>
        <x-user.ui.modal.body>
            <form action="{{ route('user.coupon.create') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label>@lang('Coupon Name') </label>
                            <input class="form-control" name="coupon_name" type="text" value="{{ old('coupon_name') }}"
                                required />
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label>@lang('Coupon Code')</label>
                            <input class="form-control" name="coupon_code" type="text" value="{{ old('coupon_code') }}"
                                required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label>@lang('Minimum Amount')</label>
                            <div class=" input--group input-group">
                                <input class="form-control" name="minimum_amount" type="number"
                                    value="{{ old('minimum_amount') }}" required step="any">
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label>@lang('Discount Type')</label>
                            <select class="form-control select2" name="discount_type" required
                                data-minimum-results-for-search="-1">
                                <option value="{{ Status::DISCOUNT_PERCENT }}">@lang('%')</option>
                                <option value="{{ Status::DISCOUNT_FIXED }}" @selected(old('discount_type') == Status::DISCOUNT_FIXED)>
                                    {{ __(gs('cur_text')) }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group input--group">
                                <input class="form-control" name="amount" type="number" value="{{ old('amount') }}"
                                    step="any" required>
                                <span class="input-group-text">@lang('%')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label>@lang('Maximum Using Time')</label>
                            <div class="input-group input--group">
                                <input class="form-control" name="maximum_using_time" type="number"
                                    value="{{ old('maximum_using_time') }}" step="any" required>
                                <span class="input-group-text">@lang('times')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label>@lang('Start From')</label>
                            <input class="form-control date-picker" name="start_from" type="text"
                                value="{{ old('start_from') }}" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label>@lang('End At')</label>
                            <input class="form-control date-picker" name="end_at" type="text"
                                value="{{ old('end_at') }}" autocomplete="off" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <x-user.ui.btn.modal />
                </div>
            </form>
        </x-user.ui.modal.body>
    </x-user.ui.modal>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            const $modal = $("#modal");

            $(".edit-btn").on('click', function(e) {

                const data = $(this).data('resource');
                const action = "{{ route('user.coupon.update', ':id') }}";

                $("input[name='coupon_name']").val(data.name);
                $("input[name='coupon_code']").val(data.code);
                $("input[name='minimum_amount']").val(getAmount(data.minimum_amount));
                $("select[name='discount_type']").val(data.discount_type);
                $("input[name='amount']").val(getAmount(data.amount));
                $("input[name='start_from']").val(data.start_from);
                $("input[name='end_at']").val(data.end_at);
                $("input[name='maximum_using_time']").val(data.maximum_using_time);
                $("textarea[name='description']").val(data.description);

                $modal.find(".modal-title").text("@lang('Edit Coupon')");
                $modal.find('form').attr('action', action.replace(':id', data.id));
                select2Initialize();
                $modal.modal("show");
            });


            $(".add-btn").on('click', function(e) {
                const action = "{{ route('user.coupon.create') }}";
                $modal.find(".modal-title").text("@lang('Add Coupon')");
                $modal.find('form').trigger('reset');
                $("select[name='discount_type']");
                $modal.find('form').attr('action', action);

                select2Initialize();
                $modal.modal("show");
            });

            $("select[name='discount_type']").on('change', function() {
                const selectedValue = $(this).val();
                if (selectedValue == "{{ Status::DISCOUNT_FIXED }}") {
                    $("input[name='amount']").attr('placeholder', "@lang('Enter fixed amount')");
                    $("input[name='amount']").siblings('.input-group-text').text(
                        "{{ gs('cur_text') }}");
                } else {
                    $("input[name='amount']").attr('placeholder', "@lang('Enter percentage')");
                    $("input[name='amount']").siblings('.input-group-text').text('%');
                }
            }).change();

            $(".date-picker").flatpickr({
                minDate: new Date(),
            });

            function select2Initialize() {
                $.each($('.select2'), function() {
                    $(this)
                        .wrap(`<div class="position-relative"></div>`)
                        .select2({
                            dropdownParent: $(this).parent(),
                        });
                });
            }

        })(jQuery);
    </script>
@endpush


@push('modal')
    <x-confirmation-modal />
@endpush

@push('breadcrumb-plugins')
<x-staff_permission_check permission="add coupon">
    <x-user.ui.btn.add tag="button" />
</x-staff_permission_check>
@endpush


@push('script-lib')
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush
