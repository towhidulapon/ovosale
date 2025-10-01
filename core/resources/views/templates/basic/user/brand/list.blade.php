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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
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
                                            <x-user.other.status_switch :status="$brand->status" :action="route('user.brand.status.change', $brand->id)"
                                                title="brand" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="brand" :id="$brand->id">
                                                <x-permission_check permission="edit brand">
                                                <x-user.ui.btn.edit tag="btn" :data-brand="$brand" />
                                                </x-permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($brands->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($brands) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add Brand')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-user.ui.modal.header>
        <x-user.ui.modal.body>
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
                const action = "{{ route('user.brand.create') }}";

                $modal.find('.modal-title').text("@lang('Add Brand')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.find('.image-upload__thumb img').attr('src',
                    "{{ asset('assets/images/drag-and-drop.png') }}");
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.brand.update', ':id') }}";
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
    <x-user.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
