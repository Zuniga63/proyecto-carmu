<div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6">
        @include('admin.dashboard.sales-credits-and-payments')
      </div>

      <div class="col-lg-6">
        @include('admin.dashboard.debt-evolution')
      </div>
    </div>
    <div>
      @include('admin.dashboard.sales-by-categories')
    </div>
    <!--/.row -->
  </div>
  <!--./container-fluid -->
</div>

@push('scripts')
<script>
  window.monthlyReports = @json($montlyReports);
  window.customersDebts = @json($creditEvolutions);
  window.salesByCategories = @json($categories);
</script>  
<script src="{{asset('assets/pages/js/admin/dashboard.js')}}?v=2.1"></script>  
@endpush