@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body class="p-0">
                    <x-admin.ui.table.layout>
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($paymentTypes as $paymentType)
                                    <tr>
                                        <td>{{ __($paymentType->name) }}</td>
                                        <td>
                                            <x-admin.other.status_switch :status="$paymentType->status" :action="route('admin.payment.type.status.change', $paymentType->id)"
                                                title="payment type" />
                                        </td>
                                        <td>
                                            @if ($paymentType->is_default == Status::NO)
                                                <x-admin.ui.btn.table_action module="payment_type" :id="$paymentType->id">
                                                    <x-permission_check permission="edit payment type">
                                                    <x-admin.ui.btn.edit tag="btn" :data-paymentType="$paymentType" />
                                                    </x-permission_check>
                                                </x-admin.ui.btn.table_action>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($paymentTypes->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($paymentTypes) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Payment Type')</h4>
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
                const action = "{{ route('admin.payment.type.create') }}";

                $modal.find('.modal-title').text("@lang('Add Payment Type')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('admin.payment.type.update', ':id') }}";
                const paymentType = $(this).data('paymenttype');

                $modal.find('.modal-title').text("@lang('Edit Payment Type')");
                $modal.find('input[name=name]').val(paymentType.name);
                $form.attr('action', action.replace(':id', paymentType.id));
                $modal.modal('show');
            });

            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.form-group').find('.iconpicker-input').val(
                    `<i class="${e.iconpickerValue}"></i>`);
            });

        })(jQuery);
    </script>
@endpush

@push('breadcrumb-plugins')
<x-permission_check permission="add payment type">
    <x-admin.ui.btn.add tag="btn" />
</x-permission_check>
@endpush



@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush


@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush
