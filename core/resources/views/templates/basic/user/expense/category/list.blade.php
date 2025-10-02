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
                                @forelse($expenseCategories as $expenseCategory)
                                    <tr>
                                        <td>{{ __($expenseCategory->name) }}</td>
                                        <td>
                                            <x-user.other.status_switch :status="$expenseCategory->status"
                                                action="{{ route('user.expense.category.status.change', $expenseCategory->id) }}"
                                                title="expense category" />
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="expense_category" :id="$expenseCategory->id">
                                                <x-permission_check permission="edit expense category">
                                                    <x-user.ui.btn.edit tag="btn" :data-expense-category="$expenseCategory" />
                                                </x-permission_check>
                                            </x-user.ui.btn.table_action>

                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($expenseCategories->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($expenseCategories) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-user.ui.modal id="modal">
        <x-user.ui.modal.header>
            <h4 class="modal-title">@lang('Add Expense Category')</h4>
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
    <x-user.ui.btn.add tag="btn" />
</x-permission_check>
@endpush
