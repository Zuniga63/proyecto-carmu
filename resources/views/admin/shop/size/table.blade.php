<div class="card {{$view === 'create' ? 'card-info' : 'card-primary'}}">
  <div class="card-header">
    <h3 class="card-title">Listado de tallas</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body table-responsive p-0" style="height: 50vh;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>Value</th>
          <th class="width70"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($sizes as $id => $value)
        <tr x-data="{id:{{$id}}, name:'{{$value}}'}">
          <td>{{$id}}</td>
          <td>{{$value}}</td>
          <td>
            <a 
              href="javascript:;" 
              class="btn btn-info" 
              data-toggle="tooltip" 
              data-placement="top"
              title="Editar este registro"
              x-on:click="$wire.edit(id)"
              {{-- wire:click="edit({{$id}})" --}}
              wire:ignore
            >
              <i class="fas fa-pencil-alt"></i>
            </a>
            <a 
              href="javascript:;" 
              class="btn btn-danger" 
              title="Eliminar permiso"
              data-toggle="tooltip" 
              data-placement="top"
              x-on:click="showDeleteAlert(id, name)"
              wire:ignore
            >
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- /. card-body -->
  
</div>