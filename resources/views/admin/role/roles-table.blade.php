<div class="card {{$view === 'create' ? 'card-success' : 'card-primary'}}">
  <div class="card-header">
    <h3 class="card-title">Listado de roles</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body table-responsive p-0" style="height: 300px;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th>Nombre </th>
          <th class="width70"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($roles as $role)
        <tr>
          <td>{{$role->name}}</td>
          <td>
            <button 
              href="#" 
              class="btn-action-table text-primary"
              wire:click="edit({{$role->id}})"
              data-toggle="tooltip" 
              data-placement="top"
              title="Editar este registro">
              <i class="fas fa-pencil-alt"></i>
            </button>
            <a 
              href="#" 
              class="btn-action-table"
              x-on:click="showDeleteAlert({{$role->id}}, '{{$role->name}}')"
              data-toggle="tooltip" 
              data-placement="top"
              title="Eliminar este registro">
              <i class="fas fa-trash text-danger"></i>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>