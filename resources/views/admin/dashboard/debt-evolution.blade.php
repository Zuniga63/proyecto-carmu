<div class="card" x-data="debtEvolutionModel()" x-init="init()">
  <div class="card-header mb-2">
    <ul class="nav nav-tabs card-header-tabs">
      <div class="nav-item">
        <a href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'graph'}" x-on:click="tab = 'graph'">Grafico</a>
      </div>
      <div class="nav-item">
        <a href="javascript:;" class="nav-link disabled" x-bind:class="{'active' : tab === 'table'}" x-on:click="tab = 'table'">Tabla</a>
      </div>
    </ul>
  </div>
  <h3 class="text-center mb-3 mt-2" x-text="title"></h3>

  {{-- SELECTORES DEL PERIODO --}}
  <div class="container">
    <div class="row justify-content-center">
      <div class="form-group row col-md-6">
        <label for="" class="col-5 col-form-label">Periodo</label>
        <select name="" id="" class="form-control col-7" x-model="periodName" x-on:change="changePeriodName">
          <option value="monthly">Mensual</option>
          {{-- <option value="quarterly">Trimestral</option> --}}
          {{-- <option value="biannual">Semestral</option> --}}
          <option value="annual">Anual</option>
          {{-- <option value="annualTremestral">Anual-Trimestral</option> --}}
          {{-- <option value="annualSemestral">Anual-Semestral</option> --}}
        </select>
      </div>

      {{-- PERIODOS MENSUALES --}}
      <div class="form-group row col-md-6" x-show.transition.in.duration.400ms="periodName === 'monthly'">
        <label for="montlyName" class="col-3 col-form-label">Mes</label>
        
        <select 
          name="montlyName" 
          id="montlyName" 
          class="form-control col-9" 
          x-model.number="month" 
          x-on:change="updateChart"
        >
          <option value="1">Enero</option>
          <option value="2">Febrero</option>
          <option value="3">Marzo</option>
          <option value="4">Abril</option>
          <option value="5">Mayo</option>
          <option value="6">Junio</option>
          <option value="7">Julio</option>
          <option value="8">Agosto</option>
          <option value="9">Septiembre</option>
          <option value="10">Octubre</option>
          <option value="11">Noviembre</option>
          <option value="12">Diciembre</option>
        </select>
      </div>
      
    </div>
  </div>

  <div class="card-body" x-show.transition="tab === 'graph'" id="debtEvolutionCanvasContainer">
    <canvas id="debtEvolution"></canvas>
  </div>

  {{-- <div class="card-body table-responsive p-0" style="height: 60vh" x-show.transition="tab === 'table'">
    <table class="table table-head-fixed table-hover text-nowrap">
      <thead>
        <tr class="text-center">
          <th>Mes</th>
          <th>Creditos</th>
          <th>Abonos</th>
          <th>Saldo</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{intval($now->format('Y')) - 1}}</td>
          <td></td>
          <td></td>
          <td class="text-right">$ {{number_format($data['customersDebts']['inititalBalance'], 0, ',', '.')}}</td>
        </tr>
        @foreach ($data['customersDebts']['reports'] as $report)
        <tr>
          <td>{{$report['month']}}</td>
          <td class="text-right">$ {{number_format($report['credits'], 0, ',', '.')}}</td>
          <td class="text-right">$ {{number_format($report['payments'], 0, ',', '.')}}</td>
          <td class="text-right">
            $ {{number_format($report['balance'], 0, ',', '.')}}
            <span class="text-small {{$report['grow'] <= 0 ? 'text-success' : 'text-danger'}}">
              ({{number_format(abs($report['grow'] * 100), 1)}}%)
            </span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div> --}}
</div>