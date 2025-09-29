@props(['user'])
@if ($user)
    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end justify-content-md-start">
        <span class="table-thumb d-none d-lg-block">
            @if (@$user->image)
                <img src="{{ $user->image_src }}" alt="user">
            @else
                <span class="name-short-form">
                    {{ __(@$user->full_name_short_form ?? 'N/A') }}
                </span>
            @endif
        </span>
        <div>
            <strong class="d-block">
                {{ __(@$user->fullname) }}
            </strong>
        </div>
    </div>
@else
    <span>@lang('N/A')</span>
@endif
