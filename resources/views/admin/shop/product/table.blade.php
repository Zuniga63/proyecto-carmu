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
        <tr x-data="{
          id:{{$item->id}}, 
          name:'{{$item->name}}',
          img: '{{$item->img ? 'Si' : 'No'}}',
          price: {{$item->price}},
          isNew: {{$item->is_new ? 'true' : 'false'}},
          outstanding: {{$item->outstanding ? 'true' : 'false'}},
          published: {{$item->published ? 'true' : 'false'}}
          }"
          x-cloak
        >
        {{-- <td x-text="id"></td> --}}
        <td>
          <img src="{{$item->img ? url('storage/' . $item->img) : url('storage/img/products/no-image-available.png')}}" width="100px" lazy>
        </td>
        <td x-text="name"></td>
        <td x-text="formatCurrencyLite(price, 0)" class="text-right"></td>
        <td>
          <div class="form-group">
            <div class="custom-control custom-switch">
              <input 
                x-bind:id="'switch-outstanding-' + id" 
                type="checkbox" 
                class="custom-control-input" 
                x-model="outstanding"
                x-on:change="$wire.changeState(id, 'outstanding', outstanding)"
              >
              <label x-bind:for="'switch-outstanding-' + id" class="custom-control-label"><i class="far fa-star"></i></label>
            </div>
            <div class="custom-control custom-switch">
              <input 
                x-bind:id="'switch-new-' + id" 
                type="checkbox" 
                class="custom-control-input" 
                x-model="isNew"
                x-on:change="$wire.changeState(id, 'isNew', isNew)"
              >
              <label x-bind:for="'switch-new-' + id" class="custom-control-label badge badge-danger">New</label>
            </div>
            <div class="custom-control custom-switch">
              <input 
                x-bind:id="'switch-public-' + id" 
                type="checkbox" 
                class="custom-control-input" 
                x-model="published"
                x-on:change="$wire.changeState(id, 'published', published)"
              >
              <label x-bind:for="'switch-public-' + id" class="custom-control-label"><i class="fas fa-globe"></i></label>
            </div>
          </div>
        </td>
        <td>
          <div class="btn-group-vertical">
            <a 
              href="#" 
              class="btn btn-info block" 
              data-toggle="tooltip" 
              data-placement="top"
              title="Editar este registro"
              wire:ignore
              wire:click="edit({{$item->id}})"
            >
              <i class="fas fa-pencil-alt"></i>
            </a>
            <a 
              href="#" 
              class="btn btn-danger" 
              title="Eliminar producto"
              data-toggle="tooltip" 
              data-placement="top"
              x-on:click="showDeleteAlert(id, name)"
              wire:ignore
            >
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