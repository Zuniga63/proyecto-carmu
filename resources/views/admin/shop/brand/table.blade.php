<div class="card {{$view === 'create' ? 'card-info' : 'card-primary'}}">
  <div class="card-header">
    <h3 class="card-title">Listado de etiquetas</h3>
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
        @foreach ($brands as $brand)
        <tr x-data="{id:{{$brand->id}}, name:'{{$brand->name}}'}">
          <td>{{$brand->id}}</td>
          <td>{{$brand->name}}</td>
          <td>{{$brand->slug}}</td>
          <td>
            <a 
              href="#" 
              class="btn btn-info" 
              data-toggle="tooltip" 
              data-placement="top"
              title="Editar este registro"
              wire:click="edit({{$brand->id}})"
            >
              <i class="fas fa-pencil-alt"></i>
            </a>
            <a 
              href="#" 
              class="btn btn-danger" 
              title="Eliminar permiso"
              data-toggle="tooltip" 
              data-placement="top"
              x-on:click="showDeleteAlert(id, name)"
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