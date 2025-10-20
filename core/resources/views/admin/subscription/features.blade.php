@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search users" filterBoxLocation="users.filter">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($planFeatures as $planFeature)
                            <tr>
                                <td>
                                    {{ $planFeature->name }}
                                </td>
                                <td>
                                    <x-admin.other.status_switch :status="$planFeature->status" :action="route('admin.subscription.feature.status', $planFeature->id)" title="Plan" />
                                </td>
                                <td>
                                    <x-admin.ui.btn.edit tag="btn" data-planfeature="{{ json_encode($planFeature) }}" :href="route('admin.subscription.feature.save', $planFeature->id)" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($planFeatures->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($planFeatures) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Plan Feature')</h4>
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

@push('breadcrumb-plugins')
    <x-permission_check permission="add new feature">
        <x-admin.ui.btn.add tag="btn" />
    </x-permission_check>
@endpush

@push('script')
    <script>
        "use strict";
        (function ($) {

            const $modal = $('#modal');
            const $form = $modal.find('form');

            $('.add-btn').on('click', function () {
                const action = "{{ route('admin.subscription.feature.save') }}";

                $modal.find('.modal-title').text("@lang('Add Plan Feature')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function () {
                const action = "{{ route('admin.subscription.feature.save', ':id') }}";
                const planFeature = $(this).data('planfeature');

                $modal.find('.modal-title').text("@lang('Edit Payment Type')");
                $modal.find('input[name=name]').val(planFeature.name);
                $form.attr('action', action.replace(':id', planFeature.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush