@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout :renderExportButton="false" filterBoxLocation="reports.purchase_filter_form"
                        :hasRecycleBin="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Invoice Number') | @lang('Reference')</th>
                                    <th>@lang('Purchase Date') | @lang('Created At')</th>
                                    <th>@lang('Supplier')</th>
                                    <th>@lang('Total Amount') | @lang('Paid Amount')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($purchases as $purchase)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block"><a
                                                        href="{{ route('user.purchase.view', $purchase->id) }}">{{ __($purchase->invoice_number) }}</a></span>
                                                <span>{{ __($purchase->reference_number ?? 'N/A') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span
                                                    class="d-block">{{ showDateTime($purchase->purchase_date, 'Y-m-d') }}</span>
                                                <span>{{ showDateTime($purchase->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __(@$purchase->supplier->name) }}</span>
                                                <span class="d-block">{{ __(@$purchase->supplier->company_name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($purchase->total) }}</span>
                                                <span
                                                    class="text--success">{{ showAmount($purchase->supplier_payments_sum_amount) }}</span>
                                            </div>
                                        </td>
                                        <td> @php echo $purchase->statusBadge @endphp </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($purchases->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($purchases) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
