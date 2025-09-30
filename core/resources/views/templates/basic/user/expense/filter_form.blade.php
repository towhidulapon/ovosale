<form action="" id="filter-form">
    @if (request()->trash)
        <input type="hidden" name="trash" value="1">
    @endif
    <x-admin.other.filter_date label="Expense Date" />
    <x-admin.other.order_by />
    <x-admin.other.per_page_record />
    <x-admin.other.filter_dropdown_btn />
</form>
