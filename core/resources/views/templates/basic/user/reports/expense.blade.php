@extends($activeTemplate . 'layouts.master')
@section('panel')

    <div class="row">
        <div class="col-12">
            <x-user.ui.card class="table-has-filter">
                <x-user.ui.card.body :paddingZero="true">
                    <x-user.ui.table.layout filterBoxLocation="reports.expense_filter_form" :hasRecycleBin="false" :renderExportButton="false">
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Purpose')</th>
                                    <th>@lang('Reference No')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Added By')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ showDateTime($expense->expense_date, 'Y-m-d') }}</td>
                                        <td>{{ __(@$expense->category->name) }}</td>
                                        <td>{{ __(@$expense->reference_no ?? 'N/A') }}</td>
                                        <td>{{ showAmount(@$expense->amount) }}</td>
                                        <td>{{ __(@$expense->user->username) }}</td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($expenses->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($expenses) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>
@endsection
