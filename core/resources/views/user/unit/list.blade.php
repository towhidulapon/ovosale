@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout>
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Short Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($units as $unit)
                                    <tr>
                                        <td>{{ __($unit->name) }}</td>
                                        <td>{{ __($unit->short_name) }}</td>
                                        <td>
                                            <x-admin.other.status_switch :status="$unit->status" :action="route('admin.unit.status.change', $unit->id)"
                                                title="unit" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.table_action module="unit" :id="$unit->id">
                                                <x-permission_check permission="edit unit">
                                                <x-admin.ui.btn.edit tag="btn" :data-unit="$unit" />
                                                </x-permission_check>
                                            </x-admin.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($units->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($units) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add unit')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label>@lang('Name')</label>
                    <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Short Name')</label>
                    <input type="text" class="form-control" name="short_name" required value="{{ old('short_name') }}">
                </div>
                <div class="form-group">
                    <x-admin.ui.btn.modal />
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
                const action = "{{ route('admin.unit.create') }}";

                $modal.find('.modal-title').text("@lang('Add Unit')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('admin.unit.update', ':id') }}";
                const unit = $(this).data('unit');

                $modal.find('.modal-title').text("@lang('Edit Unit')");
                $modal.find('input[name=name]').val(unit.name);
                $modal.find('input[name=short_name]').val(unit.short_name);
                $form.attr('action', action.replace(':id', unit.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-permission_check permission="add unit">
    <x-admin.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
