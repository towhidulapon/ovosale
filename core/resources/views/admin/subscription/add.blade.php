@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body>
                    <form action="{{ route('admin.subscription.plan.save') }}" method="POST" class="product-create-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>@lang('Plan Name')</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="required">@lang('Frequency')</label>
                                <select name="frequency" class="form-control select2">
                                    <option value="" selected disabled>@lang('Select One')</option>
                                    <option value="{{ Status::DAILY }}">
                                        @lang('Daily')
                                    </option>
                                    <option value="{{ Status::WEEKLY }}">
                                        @lang('Weekly')
                                    </option>
                                    <option value="{{ Status::MONTHLY }}">
                                        @lang('Monthly')
                                    </option>
                                    <option value="{{ Status::HALF_YEARLY }}">
                                        @lang('Half Yearly')
                                    </option>
                                    <option value="{{ Status::YEARLY }}">
                                        @lang('Yearly')
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6">
                                <label>@lang('Price')</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" step="any" name="price" required>
                                    <span class="input-group-text">
                                        {{ gs('cur_text') }}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label>@lang('Number of Warehouse')</label>
                                <input type="text" class="form-control" name="warehouse_number" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label>@lang('Trial Days')</label>
                                <input type="number" class="form-control" name="trial_days" required>
                            </div>

                            <div class="mt-4">
                                <label class="form-label fw-bold">
                                    @lang('Features') <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex flex-wrap justify-content-between">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="select_all_features">
                                            <label class="form-check-label fw-bold" for="select_all_features">
                                                @lang('Select All Features')
                                            </label>
                                        </div>
                                        @foreach($planFeatures as $feature)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input feature-checkbox" type="checkbox" name="features[]" value="{{ $feature->id }}" id="{{ $feature->name }}">
                                                <label class="form-check-label" for="{{ $feature->name }}">{{ $feature->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-admin.ui.btn.submit />
                            </div>
                        </div>
                    </form>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.feature-checkbox').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#select_all_features').prop('checked', false);
                }
            });
            $('#select_all_features').on('change', function () {
                if ($(this).is(':checked')) {
                    $('.feature-checkbox').prop('checked', true);
                } else {
                    $('.feature-checkbox').prop('checked', false);
                }
            });
        })(jQuery);
    </script>
@endpush