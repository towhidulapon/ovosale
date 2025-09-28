@extends('admin.layouts.app')
@section('panel')
    @php
        $companyInformation = gs('company_information');
    @endphp
    <form method="POST" action="{{ route('admin.setting.company.update') }}">
        <x-admin.ui.card>
            <x-admin.ui.card.body>
                @csrf
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Company Name')</label>
                            <input class="form-control" type="text" name="company_information[name]" 
                                value="{{ @$companyInformation->name }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> @lang('Email')</label>
                            <input class="form-control" type="email" name="company_information[email]"
                                value="{{ @$companyInformation->email }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> @lang('Phone')</label>
                            <input class="form-control" type="tel" name="company_information[phone]" required
                                value="{{ @$companyInformation->phone }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Address')</label>
                            <input class="form-control" type="tel" name="company_information[address]" required
                                value="{{ @$companyInformation->address }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <x-admin.ui.btn.submit />
                    </div>
                </div>
            </x-admin.ui.card.body>
        </x-admin.ui.card>
    </form>
@endsection
