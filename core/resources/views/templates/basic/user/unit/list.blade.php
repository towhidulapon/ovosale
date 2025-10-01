@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card>
                <x-user.ui.card.body :paddingZero=true>
                    <x-user.ui.table.layout>
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Short Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($units as $unit)
                                    <tr>
                                        <td>{{ __($unit->name) }}</td>
                                        <td>{{ __($unit->short_name) }}</td>
                                        <td>
                                            <x-user.other.status_switch :status="$unit->status" :action="route('user.unit.status.change', $unit->id)"
                                                title="unit" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="unit" :id="$unit->id">
                                                <x-permission_check permission="edit unit">
                                                <x-user.ui.btn.edit tag="btn" :data-unit="$unit" />
                                                </x-permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($units->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($units) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add unit')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-user.ui.modal.header>
        <x-user.ui.modal.body>
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
                    <x-user.ui.btn.modal />
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
                const action = "{{ route('user.unit.create') }}";

                $modal.find('.modal-title').text("@lang('Add Unit')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.unit.update', ':id') }}";
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
    <x-user.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
