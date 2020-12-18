<div x-data="{
    period:@entangle('period'), 
    periodCategory:@entangle('periodCategory'),
  }"
>
  <div class="row justify-content-around">
    <div class="form-group row col-md-5 col-lg-4">
      <label for="" class="col-4 col-form-label">Periodo</label>
      <select name="" id="" class="form-control col-8" x-model="period" {{-- x-on:change="setPeriod"  --}}
        {{-- x-on:input="specificPeriod = 1" --}}>
        @foreach ($periods as $key => $period)
        <option value="{{$key}}">{{$period}}</option>
        @endforeach
      </select>
    </div>
  
    <div class="form-group row col-md-5 col-lg-4">
      <label for="" class="col-4 col-form-label">Categoría</label>
      <select name="" id="" class="form-control col-8" x-model="periodCategory">
        <option value="all">Todas</option>
        @foreach ($this->categories as $key => $categoryName)
        <option value="{{$key}}">{{$categoryName}}</option>
        @endforeach
      </select>
    </div>
  
    <div class="form-group row col-md-5 col-lg-4" x-show.transition="period==='other'">
      <label for="" class="col-4 col-form-label">Desde</label>
      <select name="" id="" class="form-control col-8" {{-- x-model.number="basicPeriod"  --}}
        {{-- x-on:change="setPeriod"  --}} {{-- x-on:input="specificPeriod = 1" --}}>
        <option value="annual">Anual</option>
        <option value="biannual">Semestral</option>
        <option value="quarterly">Trimestral</option>
      </select>
    </div>
    <div class="form-group row col-md-5 col-lg-4" x-show.transition="period==='other'">
      <label for="" class="col-4 col-form-label">Hasta</label>
      <select name="" id="" class="form-control col-8" {{-- x-model.number="basicPeriod"  --}}
        {{-- x-on:change="setPeriod"  --}} {{-- x-on:input="specificPeriod = 1" --}}>
        <option value="annual">Anual</option>
        <option value="biannual">Semestral</option>
        <option value="quarterly">Trimestral</option>
      </select>
    </div>
  </div>
  <div class="row justify-content-around">
  
    <div class="col-xl-9 table-responsive p-0" style="height: 50vh;">
      <table class="table table-head-fixed table-hover text-nowrap">
        <thead>
          <tr class="text-center">
            <th>ID</th>
            <th>Fecha</th>
            <th class="text-left">Descripción</th>
            <th>Importe</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($this->sales as $sale)
          <tr>
            <td class="text-center">{{$sale['id']}}</td>
            <td class="text-center">{{$sale['date']}}</td>
            <td>{{$sale['description']}}</td>
            <td class="text-center">$ {{number_format($sale['amount'], 0, ',', '.')}}</td>
            <td class="pr-0">
              <div class="btn-group p-0">
                <button class="btn btn-info" title="Editar" data-toggle="tooltip" data-placement="top"
                  wire:click="edit({{$sale['id']}})">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger" title="Eliminar" data-placement="top" data-toggle="modal"
                  data-target="#deleteModal" {{-- {{$customer->balance > 0 ? 'disabled' : ''}} --}}
                  {{-- wire:click="$emit('save-id', {{$customer->customer_id}} )" --}}>
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  
    <div class="col-xl-3">
      <div class="card">
        <h5 class="card-header">Estadisticas</h5>
        <div class="card-body">
          <p class="card-text mb-0">Desde: {{$this->periodDates['minView']}}</p>
          <p class="card-text mb-0">Hasta: {{$this->periodDates['maxView']}}</p>
          <p class="card-text mb-0">
            Venta minima:
            <span class="text-bold">
              $ {{number_format($this->saleStatistics['min'], 0, ',', '.')}}
            </span>
          </p>
          <p class="card-text mb-0">
            Venta maxima:
            <span class="text-bold">
              $ {{number_format($this->saleStatistics['max'], 0, ',', '.')}}
            </span>
          </p>
          <p class="card-text mb-0">
            Total: <span class="text-bold">
              $ {{number_format($this->saleStatistics['total'], 0, ',', '.')}}
            </span>
          </p>
        </div>
      </div>
    </div>
  
  </div>
</div>