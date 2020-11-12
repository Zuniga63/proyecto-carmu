<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Listado de Usuarios</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body table-responsive p-0" style="height: 60vh;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Fecha de alta</th>
          <th>Rol</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($users as $user)
        <tr>
          <td>{{$user->id}}</td>
          <td>{{$user->name}}</td>
          <td>{{$user->email}}</td>
          <td>{{$this->formatDate($user->created_at)}}</td>
          <td>{{count($user->roles) > 0 ? $user->roles[0]->name : 'Ninguno'}}</td>
          <td>
            <div class="btn-group-vertical">
              <a href="#" class="btn btn-danger" title="Eliminar producto" data-toggle="tooltip" data-placement="top" wire:click="destroy({{$user->id}})">
                <i class="fas fa-trash"></i>
              </a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- /. card-body -->
</div>