@php
    $request = request();
    $categories = App\Models\ExpenseCategory::orderBy('name')->get();
@endphp

<form action="" id="filter-form">

    <div class="form-group">
        <label class="form-label">@lang('Category')</label>
        <select class="form-select select2" name="category_id">
            <option value="">@lang('All Categories')</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request()->category_id == $category->id)>
                    {{ __(@$category->name) }}
                </option>
            @endforeach
        </select>
    </div>

    <x-user.other.filter_date />
    <x-user.other.order_by />
    <x-user.other.per_page_record />
    <x-user.other.filter_dropdown_btn />

</form>
