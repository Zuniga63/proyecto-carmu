<div class="container pb-5">
  @if ($customer)
  @include('admin.carmu.customer-profile.customer-selected')
  @else
  @include('admin.carmu.customer-profile.profiles')
  @endif
</div>

