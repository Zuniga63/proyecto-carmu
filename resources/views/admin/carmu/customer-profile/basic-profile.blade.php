<div class="col-md-3">
  <div class="card">
    <div class="card-header">
      <h5 class="text-bold text-center text-nowrap text-truncate">{{$item->first_name . ' ' . $item->last_name}}</h5>
    </div>
    <div class="card-body">
      <p class="card-text text-center text-bold mb-0 text-dark" style="font-size: 1.5rem">
        $ {{number_format($item->balance, 0, '.', ' ')}}
      </p>
    </div>
    <div class="card-footer text-center">
      <a href="{{route('admin.carmu_profile', ['id' => $item->customer_id])}}" class="btn btn-primary">
        <i class="far fa-id-card"></i>
        Ir al perfil
      </a>
      <a href="{{route('admin.carmu_customers', ['id' => $item->customer_id])}}" class="btn btn-success">
        <i class="fas fa-edit"></i>
        Editar
      </a>
    </div>
  </div>
</div>
