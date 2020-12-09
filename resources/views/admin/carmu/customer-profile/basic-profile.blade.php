<div class="col-xl-3">
  <div class="card">
    <div class="card-header">
      <a href="{{route('admin.carmu_profile', ['id' => $item['id']])}}" class="d-block text-bold text-center text-nowrap text-truncate mb-0">{{$item['fullName']}}</a>
    </div>
    <div class="card-body">
      <p class="card-text text-center text-bold mb-0 {{$item['balanceColor']}}" style="font-size: 1.5rem">
        $ {{number_format($item['balance'], 0, '.', ' ')}}
      </p>
      <p class="card-text text-center text-muted mb-0">
        {{$item['state']}}
      </p>
      <p class="card-text text-center text-muted mb-0">
        {{$item['lastCredit']}}
      </p>
    </div>
    <div class="card-footer text-center d-flex justify-content-around">
      <a href="{{route('admin.carmu_profile', ['id' => $item['id']])}}" class="btn btn-success">
        <i class="fas fa-id-card"></i>
        Perfil
      </a>        
      <a href="{{route('admin.carmu_customers', ['id' => $item['id']])}}" class="btn btn-primary">
        <i class="fas fa-edit"></i>
        Editar
      </a>
    </div>
  </div>
</div>
