@props(['widget'])
<div class="row responsive-row">
    <div class="col-xxl-4 col-sm-6">
        <x-admin.ui.widget.two :url="route('admin.subscription.plan.list')" variant="primary" title="Total Plans" :value="$widget['total_plans']" icon="las la-store" />
    </div>
    <div class="col-xxl-4 col-sm-6">
        <x-admin.ui.widget.two :url="route('admin.manage.subscription.purchase')" variant="info" title="Total Purchased Plans" :value="$widget['total_purchases']" icon="las la-money-check-alt" />
    </div>
    <div class="col-xxl-4 col-sm-6">
        <x-admin.ui.widget.two :url="route('admin.manage.subscription.active')" variant="success" title="Active Plans" :value="$widget['active_plans']" icon="las la-radiation" />
    </div>
</div>
