@extends('admin.layouts.app')
@section('panel')

    <div class="row">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout filterBoxLocation="reports.expense_filter_form" :hasRecycleBin="false" :renderExportButton="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Purpose')</th>
                                    <th>@lang('Reference No')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Added By')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ showDateTime($expense->expense_date, 'Y-m-d') }}</td>
                                        <td>{{ __(@$expense->category->name) }}</td>
                                        <td>{{ __(@$expense->reference_no ?? 'N/A') }}</td>
                                        <td>{{ showAmount(@$expense->amount) }}</td>
                                        <td>{{ __(@$expense->admin->name) }}</td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($expenses->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($expenses) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
