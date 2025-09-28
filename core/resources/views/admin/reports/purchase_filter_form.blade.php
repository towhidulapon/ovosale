@php
    $request = request();
@endphp
<form action="" id="filter-form">
    @if (request()->trash)
        <input type="hidden" name="trash" value="1">
    @endif

    <div class="form-group">
        <label class="form-label">@lang('Supplier')</label>
        <x-admin.other.lazy_loading_select name="supplier_id" :required="false" :route="route('admin.supplier.lazy.loading')" />
    </div>
    <x-admin.other.filter_date />
    <x-admin.other.order_by />
    <x-admin.other.per_page_record />
    <x-admin.other.filter_dropdown_btn />
</form>

