@php
    $request = request();
@endphp

<form action="" id="filter-form">
    @if ($request->user_id)
        <input type="hidden" name="user_id" value="{{ $request->user_id }}">
    @endif
    <x-user.other.filter_date />
    <x-user.other.order_by />
    <x-user.other.per_page_record />
    <x-user.other.filter_dropdown_btn />

</form>
