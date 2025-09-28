@extends('admin.layouts.app')
@section('panel')
    <form method="POST">
        <x-admin.ui.card>
            <x-admin.ui.card.body>
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> @lang('Site Title')</label>
                            <input class="form-control" type="text" name="site_name" required value="{{ gs('site_name') }}">
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="required"> @lang('Timezone')</label>
                        <select class="  form-control select2" name="timezone">
                            @foreach ($timezones as $key => $timezone)
                                <option value="{{ @$key }}" @selected(@$key == $currentTimezone)>
                                    {{ __($timezone) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="required"> @lang('Date Format')</label>
                        <select class="form-control select2" name="date_format" data-minimum-results-for-search="-1">
                            @foreach (supportedDateFormats() as $dateFormat)
                                <option value="{{ @$dateFormat }}" @selected(gs('date_format') == $dateFormat)>
                                    {{ $dateFormat }} ({{ date($dateFormat) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="required"> @lang('Time Format')</label>
                        <select class="form-control select2" name="time_format" data-minimum-results-for-search="-1">
                            @foreach (supportedTimeFormats() as $key => $timeFormat)
                                <option value="{{ @$timeFormat }}" @selected(gs('time_format') == $timeFormat)>
                                    {{ __($timeFormat) }} ({{ date($timeFormat) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                  
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>@lang('Currency')</label>
                            <input class="form-control" type="text" name="cur_text" required
                                value="{{ gs('cur_text') }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>@lang('Currency Symbol')</label>
                            <input class="form-control" type="text" name="cur_sym" required value="{{ gs('cur_sym') }}">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 ">
                        <label class="required"> @lang('Currency Showing Format')</label>
                        <select class="select2 form-control" name="currency_format" data-minimum-results-for-search="-1">
                            <option value="1" @selected(gs('currency_format') == Status::CUR_BOTH)>
                                @lang('Show Currency Text and Symbol Both')({{ gs('cur_sym') }}{{ showAmount(100, currencyFormat: false) }}
                                {{ __(gs('cur_text')) }})
                            </option>
                            <option value="2" @selected(gs('currency_format') == Status::CUR_TEXT)>
                                @lang('Show Currency Text Only')({{ showAmount(100, currencyFormat: false) }} {{ __(gs('cur_text')) }})
                            </option>
                            <option value="3" @selected(gs('currency_format') == Status::CUR_SYM)>
                                @lang('Show Currency Symbol Only')({{ gs('cur_sym') }}{{ showAmount(100, currencyFormat: false) }})
                            </option>
                        </select>
                    </div>

                    <div class="form-group col-sm-6 ">
                        <label class="required"> @lang('Allow Precision')</label>
                        <select class="select2 form-control" name="allow_precision" data-minimum-results-for-search="-1">
                            @foreach (range(1, 8) as $digit)
                                <option value="{{ $digit }}" @selected(gs('allow_precision') == $digit)>
                                    {{ $digit }}
                                    @lang('Digit')({{ showAmount(100, currencyFormat: false, decimal: $digit) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6 ">
                        <label class="required"> @lang('Thousand Separator')</label>
                        <select class="select2 form-control" name="thousand_separator" data-minimum-results-for-search="-1">
                            @foreach (supportedThousandSeparator() as $k => $supportedThousandSeparator)
                                <option value="{{ $k }}" @selected(gs('thousand_separator') == $k)>
                                    {{ __($supportedThousandSeparator) }}
                                    @if ($k == 'space')
                                        ({{ showAmount(1000, currencyFormat: false, separator: ' ') }})
                                    @elseif($k == 'none')
                                        (@lang('10000'))
                                    @else
                                        ({{ showAmount(1000, currencyFormat: false, separator: $k) }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label> @lang('Record to Display Per Page')</label>
                        <select class="select2 form-control" name="paginate_number" data-minimum-results-for-search="-1">
                            <option value="20" @selected(gs('paginate_number') == 20)>@lang('20 items')</option>
                            <option value="50" @selected(gs('paginate_number') == 50)>@lang('50 items')</option>
                            <option value="100" @selected(gs('paginate_number') == 100)>@lang('100 items')</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <x-admin.ui.btn.submit />
                    </div>
                </div>
            </x-admin.ui.card.body>
        </x-admin.ui.card>
    </form>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel = "stylesheet" href = "{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function(color) {
                    changeColor($(this), color.toHexString())
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val();
                $(this).closest('.form-group').find('.colorPicker').spectrum({
                    color: clr,
                    change: function(color) {
                        changeColor($(this), color.toHexString());
                    }
                });
                changeColor($(this), `#${clr}`)
            });

            $.each($('.colorCode'), function(i, element) {
                const $element = $(element);
                const colorCode = `#${$element.val()}`;
                changeColor($element, colorCode);
            });

            function changeColor($this, colorCode) {
                const $parent = $this.closest('.form-group');
                $parent.find('.input-group-text').css('border-color', colorCode);
                $parent.find('.sp-replacer').css('background', colorCode);
                $parent.find('.colorCode').val(colorCode.replace(/^#?/, ''));
            }
        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        [data-theme=dark] .sp-picker-container {
            border-left: solid 1px hsl(var(--light));
            background: hsl(var(--light));
        }

        [data-theme=dark] .sp-container {
            border-color: hsl(var(--border-color)) !important;
            border: solid 1px hsl(var(--border-color));
        }
    </style>
@endpush
