<h1>Listado de permisos</h1>
<table class="table">
  <thead>
    <th>ID</th>
    <th>Nombre</th>
    <th>Slug</th>
    <th colspan="2">&nbsp;</th>
  </thead>
  <tbody>
    @foreach ($permissions as $permission)
    <tr>
      <td>{{$permission->id}}</td>
      <td>{{$permission->name}}</td>
      <td>{{$permission->slug}}</td>
      <td>
        <button class="btn btn-primary" wire:click="edit({{$permission->id}})">Editar</button>
      </td>
      <td>
        <button wire:click="destroy({{$permission->id}})" class="btn btn-danger">Eliminar</button>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
