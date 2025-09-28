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
                                    <th>@lang('Shift')</th>
                                    <th>@lang('Company')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($shifts as $shift)
                                    <tr>
                                        <td>{{ __($shift->name) }}</td>
                                        <td>{{ __(@$shift->company->name) }}</td>
                                        <td>
                                            <x-admin.other.status_switch :status="$shift->status" :action="route('admin.shift.status.change', $shift->id)"
                                                title="shift" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.table_action module="shift" :id="$shift->id">
                                                <x-permission_check permission="edit shift">
                                                    <x-admin.ui.btn.edit tag="btn" :data-shift="$shift" />
                                                </x-permission_check>
                                            </x-admin.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($shifts->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($shifts) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Shift')</h4>
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
                    <label>@lang('Company')</label>
                    <select class="form-control form--control select2" required name="company_id">
                        <option value="">@lang('Select Company')</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ __($company->name) }}
                            </option>
                        @endforeach
                    </select>
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
                const action = "{{ route('admin.shift.create') }}"
                $modal.find('.modal-title').text("@lang('Add Shift')");
                $form.trigger('reset');
                $modal.find('select[name=company_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('admin.shift.update', ':id') }}";
                const shift = $(this).data('shift');
                $modal.find('.modal-title').text("@lang('Edit Shift')");
                $modal.find('input[name=name]').val(shift.name);
                $modal.find('select[name=company_id]').val(shift.company_id).trigger('change');
                $form.attr('action', action.replace(':id', shift.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-permission_check permission="add shift">
        <x-admin.ui.btn.add tag="btn" />
    </x-permission_check>
@endpush
