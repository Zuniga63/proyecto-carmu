<div class="container-fluid">
  <div class="row">
    <div class="col-lg-3">
      @include("admin.carmu.sales.$view")
    </div>

    <div class="col-lg-9">
      @include('admin.carmu.sales.content')
    </div>
  </div>
</div>

@push('scripts')
  <script src="{{asset('js/admin/old-system/main-sales.js') . uniqid('?v=')}}"></script>

  <script>
    window.formModel = () => {
      return {
        moment:@entangle('moment'),
        date: @entangle('date'),
        categoryId: @entangle('categoryId'),
        description: @entangle('description').defer,
        amount: ''
      }
    }
  </script>
@endpush
