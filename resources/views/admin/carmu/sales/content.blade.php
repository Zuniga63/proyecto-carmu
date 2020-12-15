<div class="card" x-data="{tab:'table'}">
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
  <div class="card-body" style="height: 70vh;" x-show.transition="tab === 'table'">
    <div class="row justify-content-around">
      <div class="form-group row col-md-5 col-lg-4">
        <label for="" class="col-4 col-form-label">Periodo</label>
        <select name="" id="" class="form-control col-8" {{-- x-model.number="basicPeriod"  --}}
          {{-- x-on:change="setPeriod"  --}} {{-- x-on:input="specificPeriod = 1" --}}>
          <option value="annual">Anual</option>
          <option value="biannual">Semestral</option>
          <option value="quarterly">Trimestral</option>
        </select>
      </div>
      <div class="form-group row col-md-5 col-lg-4">
        <label for="" class="col-4 col-form-label">Desde</label>
        <select name="" id="" class="form-control col-8" {{-- x-model.number="basicPeriod"  --}}
          {{-- x-on:change="setPeriod"  --}} {{-- x-on:input="specificPeriod = 1" --}}>
          <option value="annual">Anual</option>
          <option value="biannual">Semestral</option>
          <option value="quarterly">Trimestral</option>
        </select>
      </div>
      <div class="form-group row col-md-5 col-lg-4">
        <label for="" class="col-4 col-form-label">Hasta</label>
        <select name="" id="" class="form-control col-8" {{-- x-model.number="basicPeriod"  --}}
          {{-- x-on:change="setPeriod"  --}} {{-- x-on:input="specificPeriod = 1" --}}>
          <option value="annual">Anual</option>
          <option value="biannual">Semestral</option>
          <option value="quarterly">Trimestral</option>
        </select>
      </div>

      <div class="col-xl-8 table-responsive p-0">
        @include('admin.carmu.sales.table')
      </div>

      <div class="col-xl-3">
        <div class="card">
          <h5 class="card-header">Estadisticas</h5>
          <div class="card-body">
            <p class="card-text mb-0">Desde: 01-12-2020</p>
            <p class="card-text mb-0">Hasta: 15-12-2020</p>
            <p class="card-text mb-0">Venta minima: <span class="text-bold">$200.000</span></p>
            <p class="card-text mb-0">Venta maxima: <span class="text-bold">$1.200.000</span></p>
            <p class="card-text mb-0">Total: <span class="text-bold">$1.400.000</span></p>
          </div>
        </div>
      </div>

    </div>
  </div>