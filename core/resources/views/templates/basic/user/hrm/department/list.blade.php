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
                                    <th>@lang('Department')</th>
                                    <th>@lang('Company')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($departments as $department)
                                    <tr>
                                        <td>{{ __($department->name) }}</td>
                                        <td>{{ __(@$department->company->name) }}</td>
                                        <td>
                                            <x-user.other.status_switch :status="$department->status" :action="route('user.department.status.change', $department->id)"
                                                title="department" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="department" :id="$department->id">
                                                <x-permission_check permission="edit department">
                                                    <x-user.ui.btn.edit tag="btn" :data-department="$department" />
                                                </x-permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($departments->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($departments) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add Department')</h4>
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
                const action = "{{ route('user.department.create') }}"
                $modal.find('.modal-title').text("@lang('Add Department')");
                $form.trigger('reset');
                $modal.find('select[name=company_id]').trigger('change');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.department.update', ':id') }}";
                const department = $(this).data('department');
                $modal.find('.modal-title').text("@lang('Edit Department')");
                $modal.find('input[name=name]').val(department.name);
                $modal.find('select[name=company_id]').val(department.company_id).trigger('change');
                $form.attr('action', action.replace(':id', department.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-permission_check permission="add department">
        <x-user.ui.btn.add tag="btn" />
    </x-permission_check>
@endpush
