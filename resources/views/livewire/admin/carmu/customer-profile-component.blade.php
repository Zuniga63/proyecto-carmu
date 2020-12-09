<div class="container pb-5">
  @if ($customerId)
  @include('admin.carmu.customer-profile.customer-selected')
  @else
  @include('admin.carmu.customer-profile.profiles')
  @endif
</div>

