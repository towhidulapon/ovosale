@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card class="table-has-filter">
                <x-user.ui.card.body :paddingZero="true">
                    <x-user.ui.table.layout searchPlaceholder="Search Username" filterBoxLocation="reports.filter_form" :hasRecycleBin="false">
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Employee')</th>
                                    <th>@lang('Sent')</th>
                                    <th>@lang('Sender')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            <x-admin.other.employee_info :employee="$log->employee" />
                                        </td>
                                        <td>
                                            {{ showDateTime($log->created_at) }}
                                            <br>
                                            {{ diffForHumans($log->created_at) }}
                                        </td>
                                        <td>
                                            <div>
                                                <span class="fw-bold">{{ keyToTitle($log->notification_type) }}</span> <br>
                                                @lang('via') {{ __($log->sender) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($log->subject)
                                                {{ __($log->subject) }}
                                            @else
                                                @lang('N/A')
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->notification_type == 'email')
                                                <button class="btn  btn-outline--primary notifyDetail"
                                                    data-type="{{ $log->notification_type }}"
                                                    data-message="{{ route('admin.report.email.details', $log->id) }}"
                                                    data-sent_to="{{ $log->sent_to }}">
                                                    <i class="las la-info-circle"></i>
                                                    @lang('Detail')
                                                </button>
                                            @else
                                                <button class="btn  btn-outline--primary notifyDetail"
                                                    data-type="{{ $log->notification_type }}"
                                                    data-message="{{ $log->message }}"
                                                    data-image="{{ asset(getFilePath('push') . '/' . $log->image) }}"
                                                    data-sent_to="{{ $log->sent_to }}">
                                                    <i class="las la-info-circle"></i>
                                                    @lang('Detail')
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($logs->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($logs) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="notifyDetailModal">
        <x-user.ui.modal.header>
            <h1 class="modal-title">@lang('Notification Details')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-user.ui.modal.header>
        <x-user.ui.modal.body>
            <h3 class="text-center mb-3">@lang('To'): <span class="sent_to"></span></h3>
            <div class="detail"></div>
        </x-user.ui.modal.body>
    </x-user.ui.modal>
@endsection

@if (request()->user_id)
    @push('breadcrumb-plugins')
        <a href="{{ route('admin.users.notification.single', request()->user_id) }}" class="btn btn--primary"><i
                class="fa-regular fa-paper-plane"></i>
            <span class="ms-1">@lang('Send Notification')</span>
        </a>
    @endpush
@endif

@push('script')
    <script>
        $('.notifyDetail').on('click', function() {
            var message = ''
            if ($(this).data('image')) {
                message += `<img src="${$(this).data('image')}" class="w-100 mb-2" alt="image">`;
            }
            message += $(this).data('message');
            var sent_to = $(this).data('sent_to');
            var modal = $('#notifyDetailModal');
            if ($(this).data('type') == 'email') {
                var message = `<iframe src="${message}" height="500" width="100%" title="Iframe Example"></iframe>`
            }
            $('.detail').html(message)
            $('.sent_to').text(sent_to)
            modal.modal('show');
        });
    </script>
@endpush
