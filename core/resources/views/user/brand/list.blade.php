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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td>
                                            <div class="flex-thumb-wrapper">
                                                <div class="thumb">
                                                    <img class="thumb-img" src="{{ $brand->image_src }}">
                                                </div>
                                                <span class="ms-2">{{ __($brand->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <x-admin.other.status_switch :status="$brand->status" :action="route('admin.brand.status.change', $brand->id)"
                                                title="brand" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.table_action module="brand" :id="$brand->id">
                                                <x-permission_check permission="edit brand">
                                                <x-admin.ui.btn.edit tag="btn" :data-brand="$brand" />
                                                </x-permission_check>
                                            </x-admin.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($brands->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($brands) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Brand')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>@lang('Name')</label>
                    <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>@lang('Image')</label>
                    <x-image-uploader name="image" type="brand" :required="false" />
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
                const action = "{{ route('admin.brand.create') }}";

                $modal.find('.modal-title').text("@lang('Add Brand')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.find('.image-upload__thumb img').attr('src',
                    "{{ asset('assets/images/drag-and-drop.png') }}");
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('admin.brand.update', ':id') }}";
                const brand = $(this).data('brand');

                $modal.find('.modal-title').text("@lang('Edit Brand')");
                $modal.find('input[name=name]').val(brand.name);
                $form.attr('action', action.replace(':id', brand.id));
                $modal.find('.image-upload__thumb img').attr('src', brand.image_src);
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-permission_check permission="add brand">
    <x-admin.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
