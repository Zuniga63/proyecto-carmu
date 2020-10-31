<div class="card {{$view === 'create' ? 'card-primary' : 'card-info'}}">
  <div class="card-header">
    <h3 class="card-title">Listado de productos</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body table-responsive p-0" style="height: 60vh;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th class="auto"><i class="far fa-image"></i></th>
          <th>Nombre</th>
          <th class="text-center"><i class="fas fa-dollar-sign"></i></th>
          <th>Estado</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($products as $item)
        <tr>
          <td>
            <img src="{{$item->img ? url('storage/' . $item->img) : url('storage/img/products/no-image-available.png')}}"
              width="100px" lazy>
          </td>
          <td>{{$item->name}}</td>
          <td x-text="formatCurrencyLite({{$item->price}}, 0)" class="text-right"></td>
          <td>
            <div class="form-group">
              <div class="custom-control custom-switch">
                <input id="switch-outstanding-{{$item->id}}" type="checkbox" class="custom-control-input"
                  {{$item->outstanding ? 'checked' : ''}}
                  wire:change="changeState({{$item->id}}, 'outstanding', {{$item->outstanding ? 0 : 1}})">
                <label for="switch-outstanding-{{$item->id}}" class="custom-control-label"><i class="far fa-star"></i></label>
              </div>
              <div class="custom-control custom-switch">
                <input 
                  id="switch-new-{{$item->id}}" 
                  type="checkbox" class="custom-control-input" 
                  {{$item->is_new ? 'checked' : ''}}
                  wire:change="changeState({{$item->id}}, 'isNew', {{$item->is_new ? 0 : 1}})"
                >
                <label for="switch-new-{{$item->id}}" class="custom-control-label badge badge-danger">New</label>
              </div>
              <div class="custom-control custom-switch">
                <input id="switch-public-{{$item->id}}" type="checkbox" class="custom-control-input" {{$item->published ? 'checked' : ''}}
                wire:change="changeState({{$item->id}}, 'published', {{$item->published ? 0 : 1}})">
                <label for="switch-public-{{$item->id}}" class="custom-control-label"><i class="fas fa-globe"></i></label>
              </div>
            </div>
          </td>
          <td>
            <div class="btn-group-vertical">
              <a href="#" class="btn btn-info block" data-toggle="tooltip" data-placement="top" title="Editar este registro"
                wire:ignore wire:click="edit({{$item->id}})">
                <i class="fas fa-pencil-alt"></i>
              </a>
              <a href="#" class="btn btn-danger" title="Eliminar producto" data-toggle="tooltip" data-placement="top"
                x-on:click="showDeleteAlert(id, name)" wire:ignore>
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