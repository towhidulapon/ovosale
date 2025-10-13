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
                                    <th>@lang('Staff')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-user.ui.table.header>
                            <x-user.ui.table.body>
                                @forelse($staff as $staff)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __($staff->username) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary">@lang('Action')</button>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($staff->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($staff) }}
                            </x-user.ui.table.footer>
                        @endif
                    </x-user.ui.table.layout>
                </x-user.ui.card.body>
            </x-user.ui.card>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-permission_check permission="add sale">
        <x-user.ui.btn.add href="{{ route('user.staff.create') }}" text="New Staff" />
    </x-permission_check>
@endpush