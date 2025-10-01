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
                                    <th>@lang('Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($types as $type)
                                    <tr>
                                        <td>{{ __($type->name) }}</td>
                                        <td>
                                            <x-user.other.status_switch :status="$type->status" :action="route('user.leave.type.status.change', $type->id)"
                                                title="leave type" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="leave.type" :id="$type->id">
                                                <x-permission_check permission="edit leave type">
                                                    <x-user.ui.btn.edit tag="btn" :data-type="$type" />
                                                </x-permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($types->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($types) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add Company')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-user.ui.modal.header>
        <x-user.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
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
                const action = "{{ route('user.leave.type.create') }}"
                $modal.find('.modal-title').text("@lang('Add Leave Type')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.leave.type.update', ':id') }}";
                const type = $(this).data('type');
                $modal.find('.modal-title').text("@lang('Edit Leave Type')");
                $modal.find('input[name=name]').val(type.name);
                $form.attr('action', action.replace(':id', type.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-permission_check permission="add leave type">
        <x-user.ui.btn.add tag="btn" />
    </x-permission_check>
@endpush
