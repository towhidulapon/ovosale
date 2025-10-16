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
                                    <th>@lang('Percentage')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($taxes as $tax)
                                    <tr>
                                        <td>{{ __($tax->name) }}</td>
                                        <td>{{ getAmount($tax->percentage) }}@lang('%')</td>
                                        <td>
                                            <x-user.other.status_switch :status="$tax->status" :action="route('user.tax.status.change', $tax->id)"
                                                title="tax" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="tax" :id="$tax->id">
                                                <x-staff_permission_check permission="edit tax">
                                                <x-user.ui.btn.edit tag="btn" :data-tax="$tax" />
                                                </x-staff_permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($taxes->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($taxes) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add Tax')</h4>
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
                    <label>@lang('Percentage')</label>
                    <div class="input-group input--group">
                        <input type="number" step="any" class="form-control" name="percentage" required
                            value="{{ old('percentage') }}">
                        <span class="input-group-text">@lang('%')</span>
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
        "use strict";
        (function($) {

            const $modal = $('#modal');
            const $form = $modal.find('form');

            $('.add-btn').on('click', function() {
                const action = "{{ route('user.tax.create') }}";
                $modal.find('.modal-title').text("@lang('Add Tax')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.tax.update', ':id') }}";
                const tax = $(this).data('tax');

                $modal.find('.modal-title').text("@lang('Edit Tax')");
                $modal.find('input[name=name]').val(tax.name);
                $modal.find('input[name=percentage]').val(getAmount(tax.percentage));
                $form.attr('action', action.replace(':id', tax.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-staff_permission_check permission="add tax">
    <x-user.ui.btn.add tag="btn" />
</x-staff_permission_check>
@endpush
