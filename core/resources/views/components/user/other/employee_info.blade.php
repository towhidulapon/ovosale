@props(['employee'])
@if ($employee)
    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end justify-content-md-start">
        <span class="table-thumb d-none d-lg-block">
            @if (@$employee->image)
                <img src="{{ $employee->image_src }}" alt="employee">
            @else
                <span class="name-short-form">
                   @lang('N/A')
                </span>
            @endif
        </span>
        <div>
            <strong class="d-block">
                {{ __(@$employee->name) }}
            </strong>
        </div>
    </div>
@else
    <span>@lang('N/A')</span>
@endif
