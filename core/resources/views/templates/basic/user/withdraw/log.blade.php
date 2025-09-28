@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="py-5">
        <div class="container">
            <div class="row justify-content-center mt-2">
                <div class="col-lg-12 ">
                    <div class=" d-flex justify-content-between">
                        <form>
                            <div class="mb-3 d-flex justify-content-end">
                                <div class="input-group">
                                    <input type="search" name="search" class="form-control" value="{{ request()->search }}"
                                        placeholder="@lang('Search by transactions')">
                                    <button class="input-group-text bg-primary text-white">
                                        <i class="las la-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <a href="{{ route('user.withdraw') }}">@lang('New Withdraw')</a>
                    </div>
                    <div class="card ">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th>@lang('Gateway | Transaction')</th>
                                            <th>@lang('Initiated')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Conversion')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse($withdraws as $withdraw)
                                            @php
                                                $details = [];
                                                foreach ($withdraw->withdraw_information ?? [] as $key => $info) {
                                                    $details[] = $info;
                                                    if ($info->type == 'file') {
                                                        $details[$key]->value = route(
                                                            'user.download.attachment',
                                                            encrypt(getFilePath('verify') . '/' . $info->value),
                                                        );
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="text-end text-md-center">
                                                        <span class="fw-bold"><span class="text-primary">
                                                                {{ __(@$withdraw->method->name) }}</span></span>
                                                        <br>
                                                        <small>{{ $withdraw->trx }}</small>
                                                    </div>
                                                </td>
                                                <td class="text-end text-md-center">
                                                    <div>
                                                        {{ showDateTime($withdraw->created_at) }} <br>
                                                        {{ diffForHumans($withdraw->created_at) }}
                                                    </div>
                                                </td>
                                                <td class="text-end text-md-center">
                                                    <div>
                                                        {{ showAmount($withdraw->amount) }} - <span class="text--danger"
                                                            data-bs-toggle="tooltip"
                                                            title="@lang('Processing Charge')">{{ showAmount($withdraw->charge) }}
                                                        </span>
                                                        <br>
                                                        <strong data-bs-toggle="tooltip" title="@lang('Amount after charge')">
                                                            {{ showAmount($withdraw->amount - $withdraw->charge) }}
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td class="text-end text-md-center">
                                                    <div>
                                                        {{ showAmount(1) }} =
                                                        {{ showAmount($withdraw->rate, currencyFormat: false) }}
                                                        {{ __($withdraw->currency) }}
                                                        <br>
                                                        <strong>{{ showAmount($withdraw->final_amount, currencyFormat: false) }}
                                                            {{ __($withdraw->currency) }}</strong>
                                                    </div>
                                                </td>
                                                <td class="text-end text-md-center">
                                                    <div>
                                                        @php echo $withdraw->statusBadge @endphp
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn  btn--base detailBtn"
                                                        data-user_data="{{ json_encode($details) }}"
                                                        @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                                                        <i class="las la-info-circle"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($withdraws->hasPages())
                            <div class="card-footer">
                                {{ paginateLinks($withdraws) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData list-group-flush">

                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark " data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    } else {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                        </li>`;
                    }
                });
                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);

                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        })(jQuery);
    </script>
@endpush
