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
  <script src="{{asset("assets/$theme/plugins/chart.js/Chart.min.js")}}"></script>
  {{-- <script src="{{asset('js/admin/old-system/main-sales.js') . uniqid('?v=')}}"></script> --}}
  <script src="{{asset('js/admin/old-system/main-sales.js') . '?v=2.0'}}"></script>

  <script>
    window.formModel = () => {
      return {
        moment:@entangle('moment').defer,
        type: @entangle('saleType').defer,
        date: @entangle('date').defer,
        setTime: @entangle('setTime').defer,
        time: @entangle('time').defer,
        categoryId: @entangle('categoryId').defer,
        description: @entangle('description').defer,
        amount: ''
      }
    }
  </script>
@endpush
