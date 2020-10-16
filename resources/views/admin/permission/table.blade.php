<div class="card card-info">
  <div class="card-header">
    <h3 class="card-title">Listado de permisos</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body table-responsive p-0" style="height: 300px;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Slug</th>
          <th class="width70"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($permissions as $permission)
        <tr>
          <td>{{$permission->id}}</td>
          <td>{{$permission->name}}</td>
          <td>{{$permission->slug}}</td>
          <td>
            <a href="#" class="btn-action-table tooltipsC" title="Editar este registro"
              wire:click="edit({{$permission->id}})">
              <i class="fas fa-pencil-alt"></i>
            </a>
            {{-- <a href="#" class="btn-action-table delete tooltipsC"
              title="Eliminar permiso" wire:click="destroy({{$permission->id}})">
            <i class="fas fa-trash text-danger"></i>
            </a> --}}
            <a href="#" class="btn-action-table delete tooltipsC" title="Eliminar permiso"
              wire:click="$emit('triggerDelete',{{ $permission->id }})">
              <i class="fas fa-trash text-danger"></i>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- /. card-body -->
  
</div>