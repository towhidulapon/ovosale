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
                                    <th>@lang('Email')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Address')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($companies as $company)
                                    <tr>
                                        <td>{{ __($company->name) }}</td>
                                        <td>{{ $company->email }}</td>
                                        <td>{{ $company->mobile }}</td>
                                        <td>{{ __($company->address) }}</td>
                                        <td>{{ __($company->country) }}</td>
                                        <td>
                                            <x-user.other.status_switch :status="$company->status" :action="route('user.company.status.change', $company->id)"
                                                title="company" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="company" :id="$company->id">
                                                <x-staff_permission_check permission="edit company">
                                                    <x-user.ui.btn.edit tag="btn" :data-company="$company" />
                                                </x-staff_permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($companies->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($companies) }}
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
                <div class="form-group">
                    <label>@lang('Name')</label>
                    <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Email')</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Mobile')</label>
                    <input type="tel" class="form-control" name="mobile" value="{{ old('mobile') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Address')</label>
                    <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Country')</label>
                    <input type="text" class="form-control" name="country" value="{{ old('country') }}">
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
                const action = "{{ route('user.company.create') }}"
                $modal.find('.modal-title').text("@lang('Add Company')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.company.update', ':id') }}";
                const company = $(this).data('company');
                $modal.find('.modal-title').text("@lang('Edit Company')");
                $modal.find('input[name=name]').val(company.name);
                $modal.find('input[name=email]').val(company.email);
                $modal.find('input[name=mobile]').val(company.mobile);
                $modal.find('input[name=country]').val(company.country);
                $modal.find('input[name=address]').val(company.address);
                $form.attr('action', action.replace(':id', company.id));
                $modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
    <x-staff_permission_check permission="add company">
        <x-user.ui.btn.add tag="btn" />
    </x-staff_permission_check>
@endpush
