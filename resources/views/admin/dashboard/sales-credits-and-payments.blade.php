<div class="card" x-data="monthlyReportsModel()">
  <div class="card-header mb-2">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a  href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'graph'}" x-on:click="tab = 'graph'">Grafico</a>
      </li>
      <li class="nav-item">
        <a href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'table'}" x-on:click="tab = 'table'">Tabla</a>
      </li>
    </ul>
  </div>
  <h3 class="text-center mb-2">Ventas, Creditos y Abonos [{{$now->format('Y')}}]</h3>
  <div class="card-body table-responsive p-0" style="height: 60vh" x-show.transition="tab === 'table'">
    <table class="table table-head-fixed table-hover text-nowrap">
      <thead>
        <tr class="text-center">
          <th>Mes</th>
          <th>Ventas</th>
          <th>Abonos</th>
          <th>Creditos</th>
          <th>Balance</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($montlyReports['reports'] as $report)
        <tr>
          <td>{{$report['month']}}</td>
          <td class="text-right">$ {{number_format($report['sales'], 0, ',', '.')}}</td>
          <td class="text-right">$ {{number_format($report['payments'], 0, ',', '.')}}</td>
          <td class="text-right">$ {{number_format($report['credits'], 0, ',', '.')}}</td>
          <td class="text-right {{$report['balance'] >= 0 ? 'text-success' : 'text-danger'}}">$
            {{number_format($report['balance'], 0, ',', '.')}}</td>
        </tr>
        @endforeach
        <tr class="text-bold">
          <td>Total:</td>
          <td>$ {{number_format($montlyReports['totalSales'], 0, ',', '.')}}</td>
          <td>$ {{number_format($montlyReports['totalPayments'], 0, ',', '.')}}</td>
          <td>$ {{number_format($montlyReports['totalCredits'], 0, ',', '.')}}</td>
          <td>$ {{number_format($montlyReports['totalBalance'], 0, ',', '.')}}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="card-body" x-show.transition="tab === 'graph'">
    <div class="container">
      <div class="row justify-content-center">
        <div class="form-group row col-md-6">
          <label for="" class="col-5 col-form-label">Periodo</label>
          <select name="" id="" class="form-control col-7" x-model.number="basicPeriod" x-on:change="setPeriod" x-on:input="specificPeriod = 1">
            <option value="annual">Anual</option>
            <option value="biannual">Semestral</option>
            <option value="quarterly">Trimestral</option>
          </select>
        </div>

        <div class="form-group row col-md-6" x-show.transition="basicPeriod === 'biannual'">
          <label for="" class="col-6 col-form-label">Semestre</label>
          <select name="" id="" class="form-control col-6" x-model.number="specificPeriod" x-on:change="setPeriod">
            <option value="1">Ene - Jun</option>
            <option value="2">Jul - Dic</option>
          </select>
        </div>
        
        <div class="form-group row col-md-6" x-show.transition="basicPeriod === 'quarterly'">
          <label for="" class="col-6 col-form-label">Trimestre</label>
          <select name="" id="" class="form-control col-6" x-model.number="specificPeriod" x-on:change="setPeriod">
            <option value="1">Ene - Mar</option>
            <option value="2">Abr - Jun</option>
            <option value="3">Jul - Sep</option>
            <option value="4">Oct - Dic</option>
          </select>
        </div>
      </div>
    </div>
    <canvas id="monthlyReports"></canvas>
  </div>
</div>