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
                                    <th>@lang('Attribute')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($variants as $variant)
                                    <tr>
                                        <td>{{ __($variant->name) }}</td>
                                        <td>{{ __(@$variant->attribute->name) }}</td>
                                        <td>
                                            <x-user.other.status_switch :status="$variant->status" :action="route('user.variant.status.change', $variant->id)"
                                                title="variant" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="variant" :id="$variant->id">
                                                <x-permission_check permission="edit variant">
                                                <x-user.ui.btn.edit tag="btn" :data-variant="$variant" />
                                                </x-permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($variants->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($variants) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add variant')</h4>
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
                    <label>@lang('Attribute')</label>
                    <select name="attribute" required class="form-control select2">
                        <option value="">@lang('Select One')</option>
                        @foreach ($attributes as $attribute)
                            <option value="{{ $attribute->id }}">
                                {{ __($attribute->name) }}
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
                const action = "{{ route('user.variant.create') }}";

                $modal.find('.modal-title').text("@lang('Add Variant')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.find('select[name=attribute]').val('');
                select2Initialize();
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.variant.update', ':id') }}";
                const variant = $(this).data('variant');

                $modal.find('.modal-title').text("@lang('Edit Variant')");
                $modal.find('input[name=name]').val(variant.name);
                $modal.find('select[name=attribute]').val(variant.attribute_id);
                $form.attr('action', action.replace(':id', variant.id));

                select2Initialize();
                $modal.modal('show');
            });

            function select2Initialize() {
                $.each($('.select2'), function() {
                    $(this)
                        .wrap(`<div class="position-relative"></div>`)
                        .select2({
                            dropdownParent: $(this).parent(),
                        });
                });
            }

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-permission_check permission="add variant">
    <x-user.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
