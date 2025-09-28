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
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="flex-thumb-wrapper">
                                                <div class="thumb">
                                                    <img class="thumb-img" src="{{ $category->image_src }}">
                                                </div>
                                                <span class="ms-2">{{ __($category->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <x-admin.other.status_switch :status="$category->status" :action="route('admin.category.status.change', $category->id)"
                                                title="category" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.table_action module="category" :id="$category->id">
                                                <x-permission_check permission="edit category">
                                                <x-admin.ui.btn.edit tag="btn" :data-category="$category" />
                                                </x-permission_check>
                                            </x-admin.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($categories->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($categories) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Category')</h4>
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
                    <x-image-uploader name="image" type="category" :required="false" />
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
                const action = "{{ route('admin.category.create') }}";

                $modal.find('.modal-title').text("@lang('Add Category')");
                $modal.find('.image-upload__thumb img').attr('src',
                    "{{ asset('assets/images/drag-and-drop.png') }}");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('admin.category.update', ':id') }}";
                const category = $(this).data('category');

                $modal.find('.modal-title').text("@lang('Edit Category')");
                $modal.find('.image-upload__thumb img').attr('src', category.image_src);
                $modal.find('input[name=name]').val(category.name);
                $form.attr('action', action.replace(':id', category.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-permission_check permission="add category">
    <x-admin.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
