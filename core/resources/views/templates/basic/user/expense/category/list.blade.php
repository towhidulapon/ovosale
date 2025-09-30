@extends($activeTemplate . 'layouts.master')
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
                                @forelse($expenseCategories as $expenseCategory)
                                    <tr>
                                        <td>{{ __($expenseCategory->name) }}</td>
                                        <td>
                                            <x-admin.other.status_switch :status="$expenseCategory->status"
                                                action="{{ route('user.expense.category.status.change', $expenseCategory->id) }}"
                                                title="expense category" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.table_action module="expense_category" :id="$expenseCategory->id">
                                                <x-permission_check permission="edit expense category">
                                                    <x-admin.ui.btn.edit tag="btn" :data-expense-category="$expenseCategory" />
                                                </x-permission_check>
                                            </x-admin.ui.btn.table_action>

                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($expenseCategories->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($expenseCategories) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Add Expense Category')</h4>
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
                const action = "{{ route('user.expense.category.create') }}";

                $modal.find('.modal-title').text("@lang('Add Expense Category')");
                $form.trigger('reset');
                $form.attr('action', action);
                $modal.modal('show');
            });

            $('.edit-btn').on('click', function() {
                const action = "{{ route('user.expense.category.update', ':id') }}";
                const expenseCategory = $(this).data('expense-category');

                $modal.find('.modal-title').text("@lang('Edit Expense Category')");
                $modal.find('input[name=name]').val(expenseCategory.name);
                $form.attr('action', action.replace(':id', expenseCategory.id));
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
@push('breadcrumb-plugins')
<x-permission_check permission="add expense category">
    <x-admin.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
