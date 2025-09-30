<div class="table-filter">
    <div class=" dropdown">
        <button class="btn btn-outline--secondary w-100  dropdown-toggle" type="button" data-bs-toggle="dropdown"
            data-bs-auto-close="outside">
            <span class="icon">
                <i class="las la-sort"></i>
            </span>
            @lang('Filter')
        </button>
        <div class="dropdown-menu dropdown-menu-filter-box">
            @if (!$filterBoxLocation)
                @php
                    $request = request();
                @endphp
                <form action="" id="filter-form">
                    @if (request()->trash)
                        <input type="hidden" name="trash" value="1">
                    @endif

                    <x-user.other.order_by />
                    <x-user.other.per_page_record />
                    <x-user.other.filter_dropdown_btn />
                </form>
            @else
                @include("Template::user.$filterBoxLocation")
            @endif
        </div>
    </div>
</div>
