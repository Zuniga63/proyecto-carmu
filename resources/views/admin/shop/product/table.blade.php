<div class="card {{$view === 'create' ? 'card-primary' : 'card-info'}}">
  <div class="card-header">
    <h3 class="card-title">Listado de productos</h3>
    <div class="card-tools" 
      x-data="{
        barcode:'', 
        async sendData(){
          if(this.barcode){
            await $wire.searchBarcode(this.barcode);
            this.barcode='';
          }
        }
      }" 
      wire:ignore
    >
      <div class="input-group input-group-sm" style="width: 150px;">
        <input type="text" name="table_search" class="form-control float-right" placeholder="Codigo" x-model.trim="barcode" x-on:keydown.enter="sendData">

        <div class="input-group-append">
          <button x-on:click="sendData" class="btn btn-default"><i class="fas fa-search"></i></button>
        </div>
      </div>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body table-responsive p-0" style="height: 80vh;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th class="auto"><i class="far fa-image"></i></th>
          <th>Información</th>
          <th>Estado</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($products as $item)
        <tr x-data="{id:{{$item->id}}, name:'{{$item->name}}'}">
          <td>
            <img src="{{$item->img ? url('storage/' . $item->img) : url('storage/img/products/no-image-available.png')}}"
              width="80px" loading="lazy" class="border rounded p-1 bg-secondary">
          </td>
          <td>
            <p class="mb-0">{{$item->name}}</p>
            <p class="mb-0">Precio: <span class="text-bold" x-text="formatCurrency({{$item->price}}, 0)"></span></p>
            <p class="mb-0">Codigo: <span class="text-bold">{{$item->barcode ? $item->barcode : 'N/A'}}</span></p>
          </td>
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
              <a href="javascript:;" class="btn btn-info block" data-toggle="tooltip" data-placement="top" wire:click="edit({{$item->id}})">
                <i class="fas fa-pencil-alt"></i>
              </a>
              <a 
                href="javascript:;" 
                class="btn btn-danger" 
                title="Eliminar producto" 
                data-toggle="tooltip" data-placement="top"
                x-on:click="showDeleteAlert(id, name)"
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