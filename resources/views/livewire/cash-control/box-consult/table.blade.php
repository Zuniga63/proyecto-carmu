<div class="card card-info">
  <div class="card-header mb-3">
    <h3 class="card-title">Transacciones</h3>
  </div>
  <!-- /.card-header -->

  <div class="container-fluid">
    {{-- Negocio, Caja y Categorías --}}
    <div class="row justify-content-center">
      {{-- selección del negocio --}}
      <div class="form-group row col-md-4">
        <label for="business" class="col-5 col-form-label">Negocio</label>
        <select name="business" id="business" class="form-control col-7" wire:model="businessId">
          <option value="0" selected disabled>Selecciona uno</option>
          @foreach ($business as $id => $name)
          <option value="{{$id}}">{{$name}}</option>
          @endforeach
        </select>
      </div>

      {{-- Selección de la categoría --}}
      <div class="form-group row col-md-4">
        <label for="type" class="col-3 col-form-label">Categoría</label>
        
        <select 
          name="type" 
          id="type" 
          class="form-control col-9" 
          wire:model="type"
        >
          <option value="all">Todas las categorías</option>
          @foreach ($transactionType as $key => $value)
          <option value="{{$key}}">{{$value}}</option>
          @endforeach
        </select>
      </div>

      {{-- Selección de la caja --}}
      <div class="form-group row col-md-4">
        <label for="box" class="col-3 col-form-label">Caja</label>
        
        <select 
          name="montlyName" 
          id="box" 
          class="form-control col-9" 
          wire:model="boxId"
        >
          <option value="0">Todas las cajas</option>
          @foreach ($boxes as $id => $name)
          <option value="{{$id}}">{{$name}}</option>
          @endforeach
        </select>
      </div>      
    </div>

    {{-- Fechas --}}
    <div class="row justify-content-around">
      {{-- Desde que fecha se consulta --}}
      <div class="form-group row col-md-4">
        <label for="since" class="col-5 col-form-label">Desde</label>
        <input
          type="date" 
          name="since" 
          id="since" 
          class="form-control col-7" 
          wire:model="since"
          max="{{ $this->maxDate }}"
        >
      </div>

      {{-- Hasta que fecha se consulta --}}
      <div class="form-group row col-md-4">
        <label for="until" class="col-3 col-form-label">Hasta</label>
        
        <input
          type="date" 
          name="until" 
          id="until" 
          class="form-control col-9" 
          wire:model="until"
          max="{{ $this->maxDate }}"
        >
      </div>
      
    </div>
  </div>

  <div class="card-body table-responsive p-0" style="height: 300px;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap" x-data>
      <thead>
        <tr class="text-center">
          <th>ID</th>
          <th>Caja</th>
          <th>Fecha</th>
          <th class="text-left">Descripción</th>
          <th>Tipo</th>
          <th>Importe</th>
          <th>Acumulado</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($this->transactions as $record)
        <tr>
          <td class="text-center">{{ $record['id'] }}</td>
          <td class="text-center">{{ $record['box'] }}</td>
          <td class="text-center">{{ $record['date'] }}</td>
          <td class="text-left">{{ $record['description'] }}</td>
          <td class="text-center">{{ $record['type'] }}</td>
          <td class="text-right" x-text="formatCurrency({{ $record['amount'] }}, 0)"></td>
          <td class="text-right" x-text="formatCurrency({{ $record['accumulated'] }}, 0)"></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- /. card-body -->
  
</div>