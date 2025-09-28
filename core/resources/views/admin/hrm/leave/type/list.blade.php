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
                                    <th>@lang('Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($types as $type)
                                    <tr>
                                        <td>{{ __($type->name) }}</td>
                                        <td>
                                            <x-admin.other.status_switch :status="$type->status" :action="route('admin.leave.type.status.change', $type->id)"
                                                title="leave type" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.table_action module="leave.type" :id="$type->id">
                                                <x-permission_check permission="edit leave type">
                                                    <x-admin.ui.btn.edit tag="btn" :data-type="$type" />
                                                </x-permission_check>
                                            </x-admin.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($types->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($types) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Company')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
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
                const action = "{{ route('admin.leave.type.create') }}"
                $modal.find('.modal-title').text("@lang('Add Leave Type')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('admin.leave.type.update', ':id') }}";
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
        <x-admin.ui.btn.add tag="btn" />
    </x-permission_check>
@endpush
