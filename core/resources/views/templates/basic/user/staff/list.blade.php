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
                                @forelse($staffs as $staff)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __($staff->username) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <x-user.ui.btn.table_action module="staff" :id="$staff->id">
                                                <x-staff_permission_check permission="edit staff">
                                                    <x-user.ui.btn.edit tag="a" href="{{ route('user.staff.edit', $staff->id) }}" />
                                                    <a href="{{ route('user.staff.permissions', $staff->id) }}" class="btn btn-outline--info">
                                                        <i class="fas fa-user-check"></i> @lang('Permissions')
                                                    </a>
                                                </x-staff_permission_check>
                                            </x-user.ui.btn.table_action>
                                        </td>
                                    </tr>
                                @empty
                                    <x-user.ui.table.empty_message />
                                @endforelse
                            </x-user.ui.table.body>
                        </x-user.ui.table>
                        @if ($staffs->hasPages())
                            <x-user.ui.table.footer>
                                {{ paginateLinks($staffs) }}
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
    <x-staff_permission_check permission="add sale">
        <x-user.ui.btn.add href="{{ route('user.staff.create') }}" text="New Staff" />
    </x-staff_permission_check>
@endpush