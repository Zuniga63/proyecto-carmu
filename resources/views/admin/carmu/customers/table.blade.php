<div class="card {{$view === 'create' ? 'card-primary' : 'card-info'}}">
  <div class="card-header">
    <div class="card-title">Listado de Clientes</div>
    <div class="card-tools">
      <div class="input-group input-group-sm" style="width: 150px">
        <input type="text" class="form-control float-right" placeholder="Bucar" wire:model="search">
        <div class="input-group-append">
          <button class="btn btn-default"><i class="fas fa-search"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body table-responsive p-0" style="height: 70vh;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombres y Apellidos</th>
          <th>Telefono</th>
          <th>Saldo</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($this->customers as $customer)
        <tr class="{{$customer->archived ? 'text-muted' : ''}}">
          <td>{{$customer->customer_id}}</td>
          <td>{{$customer->first_name}}  {{$customer->last_name}}</td>
          <td>{{$customer->phone}}</td>
          <td class="text-right">${{number_format($customer->balance, 0, ".", ' ')}}</td>
          <td class="pr-0">
            <div class="btn-group p-0">
              <button 
                class="btn btn-info" title="Editar Cliente" 
                data-toggle="tooltip" 
                data-placement="top" 
                wire:click="edit({{$customer->customer_id}})"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button 
                class="btn btn-secondary" 
                title="{{$customer->archived ? 'Desarchivar' : 'Archivar'}}" 
                data-toggle="tooltip" 
                data-placement="top" 
                wire:click="archived({{$customer->customer_id}})" 
              >
                <i class="fas {{$customer->archived ? 'fa-folder-open' : 'fa-folder'}}"></i>
              </button>
              <button 
                class="btn btn-danger" 
                title="Eliminar Cliente"
                data-placement="top"
                data-toggle="modal" data-target="#deleteModal"
                {{$customer->balance > 0 ? 'disabled' : ''}}
                wire:click="$emit('save-id', {{$customer->customer_id}} )"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted text-center text-bold d-md-flex justify-content-around">
    <p>Clientes: {{count($this->customers)}}</p>
    <p>Circulando: ${{number_format($this->balance, 0, '.', ' ')}}</p>
    <p>Incobrable: ${{number_format($this->archivedBalance, 0, '.', ' ')}}</p>
  </div>
</div>