<div class="card" 
  x-data="{
    tab:'table', 
    period:@entangle('period'), 
    periodCategory:@entangle('periodCategory')
  }"
>
  <div class="card-header mb-2">
    <div class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a href="#" class="nav-link px-1 px-sm-4" x-bind:class="{'active' : tab === 'table'}"
          x-on:click="tab = 'table'" disabled>Datos</a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link px-1 px-sm-4" x-bind:class="{'active' : tab === 'graph'}"
          x-on:click="tab = 'graph'">Gráficas</a>
      </li>
    </div>
    {{-- /. nav --}}
  </div>
  {{-- ./ card-header --}}
  <div class="card-body" x-show.transition="tab === 'table'">
    <div class="row justify-content-around">
      <div class="form-group row col-md-5 col-lg-4">
        <label for="" class="col-4 col-form-label">Periodo</label>
        <select name="" id="" class="form-control col-8" x-model="period" 
          {{-- x-on:change="setPeriod"  --}} {{-- x-on:input="specificPeriod = 1" --}}>
          @foreach ($periods as $key => $period)
          <option value="{{$key}}">{{$period}}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group row col-md-5 col-lg-4">
        <label for="" class="col-4 col-form-label">Categoría</label>
        <select name="" id="" class="form-control col-8" x-model="periodCategory" 
        >
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
        @include('admin.carmu.sales.table')
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