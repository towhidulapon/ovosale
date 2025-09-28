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
                                    <th>@lang('Percentage')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($taxes as $tax)
                                    <tr>
                                        <td>{{ __($tax->name) }}</td>
                                        <td>{{ getAmount($tax->percentage) }}@lang('%')</td>
                                        <td>
                                            <x-admin.other.status_switch :status="$tax->status" :action="route('admin.tax.status.change', $tax->id)"
                                                title="tax" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.table_action module="tax" :id="$tax->id">
                                                <x-permission_check permission="edit tax">
                                                <x-admin.ui.btn.edit tag="btn" :data-tax="$tax" />
                                                </x-permission_check>
                                            </x-admin.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($taxes->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($taxes) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Tax')</h4>
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
                    <label>@lang('Percentage')</label>
                    <div class="input-group input--group">
                        <input type="number" step="any" class="form-control" name="percentage" required
                            value="{{ old('percentage') }}">
                        <span class="input-group-text">@lang('%')</span>
                    </div>
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
                const action = "{{ route('admin.tax.create') }}";
                $modal.find('.modal-title').text("@lang('Add Tax')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('admin.tax.update', ':id') }}";
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
<x-permission_check permission="add tax">
    <x-admin.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
