@extends('admin.layouts.app')
@section('panel')
    <div class="row  responsive-row justify-content-center">
        <div class="col-5">
            <x-admin.ui.card>
                <x-admin.ui.card.header class="d-flex gap-2 justify-content-between flex-wrap align-items-center">
                    <div class="">
                        <h4 class="card-title mb-0">@lang('DATABASE BACKUP')</h4>
                        <span class="fs-14  text-muted">@lang('Create your database backup')</span>
                    </div>
                    <h2 class="text--info mb-0"><i class="fas fa-database"></i></h2>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body class="p-0">
                    <div class="mb-3">
                        @if ($backups->count())
                            <x-admin.ui.table>
                                <x-admin.ui.table.header>
                                    <tr>
                                        <th>@lang('File')</th>
                                        <th>@lang('Create By')</th>
                                        <th>@lang('Since')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </x-admin.ui.table.header>
                                <x-admin.ui.table.body>
                                    @foreach ($backups as $backup)
                                        <tr>

                                        </tr>
                                    @endforeach
                                </x-admin.ui.table.body>
                            </x-admin.ui.table>
                        @else
                            <div class="p-5 text-center">
                                <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                                <span class="d-block">@lang('No recent backup found')</span>
                                <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this card at the moment.')</span>
                                <a href="{{ route('admin.database.backup.create') }}" class="btn btn-large btn--primary mt-3">@lang('CREATE YOUR FIRST BACKUP NOW')</a>
                            </div>
                        @endif
                    </div>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
