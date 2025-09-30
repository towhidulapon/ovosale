@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-user.ui.card class="table-has-filter">
                <x-user.ui.card.body :paddingZero="true">
                    <x-user.ui.table.layout :renderExportButton="false" filterBoxLocation="reports.purchase_filter_form"
                        :hasRecycleBin="false">
                        <x-user.ui.table>
                            <x-user.ui.table.header>
                                <tr>
                                    <th>@lang('Invoice Number') | @lang('Reference')</th>
                                    <th>@lang('Purchase Date') | @lang('Created At')</th>
                                    <th>@lang('Supplier')</th>
                                    <th>@lang('Total Amount') | @lang('Paid Amount')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
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
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($purchases->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($purchases) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>
@endsection
