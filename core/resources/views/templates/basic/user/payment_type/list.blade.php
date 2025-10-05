@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card>
                <x-user.ui.card.body class="p-0">
                    <x-user.ui.table.layout>
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($paymentTypes as $paymentType)
                                    <tr>
                                        <td>{{ __($paymentType->name) }}</td>
                                        <td>
                                            <x-user.other.status_switch :status="$paymentType->status" :action="route('user.payment.type.status.change', $paymentType->id)"
                                                title="payment type" />
                                        </td>
                                        <td>
                                            @if ($paymentType->is_default == Status::NO)
                                                <x-user.ui.btn.table_action module="payment_type" :id="$paymentType->id">
                                                    <x-permission_check permission="edit payment type">
                                                    <x-user.ui.btn.edit tag="btn" :data-paymentType="$paymentType" />
                                                    </x-permission_check>
                                                </x-user.ui.btn.table_action>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($paymentTypes->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($paymentTypes) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add Payment Type')</h4>
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
                const action = "{{ route('user.payment.type.create') }}";

                $modal.find('.modal-title').text("@lang('Add Payment Type')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.payment.type.update', ':id') }}";
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
    <x-user.ui.btn.add tag="btn" />
</x-permission_check>
@endpush



@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush


@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/fontawesome-iconpicker.js') }}"></script>
@endpush
