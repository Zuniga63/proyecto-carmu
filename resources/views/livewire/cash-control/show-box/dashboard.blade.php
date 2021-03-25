@foreach ($this->boxs as $box)
<div class="col-lg-6" x-data>
  <div class="card {{$box['business'] == 'Tienda Carmú' ? 'card-success' : ($box['business'] == 'Son de Cuatro' ? 'card-primary' : 'card-dark')}}">
    <div class="card-header text-center">
      <h5 class="text-bold mb-0">{{$box['name']}}</h5>
      <p class="m-0" style="font-size: 0.8em">({{$box['business']}})</p>
    </div>
    <div class="card-body p-2">
      @include('livewire.cash-control.show-box.box-info', $box)
    </div><!--/.end body -->

    <div class="card-footer row justify-content-between">
      <div class="col-6">
        <a href="{{route('admin.showBox', $box['id'])}}" class="btn {{$box['business'] == 'Tienda Carmú' ? 'btn-success' : ($box['business'] == 'Son de Cuatro' ? 'btn-primary' : 'btn-dark')}} col-12">Administrar Caja</a> 
      </div>
      <div class="col-6">
        <a href="javascript:;" class="btn btn-secondary col-12" wire:click="render">Actualizar</a> 
      </div>
    </div>
  </div><!--/.end card -->
</div><!--/.end col -->
@endforeach