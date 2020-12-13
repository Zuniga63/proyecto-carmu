<div class="col-xl-3">
  <div class="card">
    <div class="card-header">
      <a href="{{route('admin.carmu_profile', ['id' => $item['id']])}}" class="d-block text-bold text-center text-nowrap text-truncate mb-0">{{$item['fullName']}}</a>
    </div>
    <div class="card-body position-relative">
      @if ($item['time'] > 0 && $item['time'] <= 1)
      <i class="far fa-grin-hearts position-absolute text-success" style="font-size:2rem;"></i>
      @elseif ($item['time'] > 0 && $item['time'] <= 2)
      <i class="far fa-smile position-absolute text-success" style="font-size:2rem;"></i>
      @elseif ($item['time'] <= 2.5)
      <i class="far fa-meh position-absolute" style="font-size:2rem;"></i>
      @elseif ($item['time'] <= 3)
      <i class="far fa-meh-blank position-absolute text-warning" style="font-size:2rem;"></i>
      @elseif ($item['time'] <= 4)
      <i class="far fa-frown position-absolute text-warning" style="font-size:2rem;"></i>
      @elseif ($item['time'] <= 10)
      <i class="far fa-angry position-absolute text-danger" style="font-size:2rem;"></i>
      @else
      <i class="far fa-dizzy position-absolute text-danger" style="font-size:2rem;"></i>

      @endif
      <p class="card-text text-center text-bold mb-0 {{$item['balanceColor']}}" style="font-size: 1.5rem">
        $ {{number_format($item['balance'], 0, '.', ' ')}}
      </p>
      <p class="card-text text-center text-muted mb-0">
        {{$item['state']}}
      </p>
      <p class="card-text text-center text-muted mb-0">
        {{$item['lastCredit']}}
      </p>
      
      {{-- <p class="card-text text-center text-muted mb-0">
        {{$item['time']}}
      </p> --}}
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
