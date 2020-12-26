<div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6">
        <div class="card" x-data="monthlyReportsModel()">
          <div class="card-header mb-2">
            <ul class="nav nav-tabs card-header-tabs">
              <li class="nav-item">
                <span  class="nav-link" x-bind:class="{'active' : tab === 'graph'}" x-on:click="tab = 'graph'">Grafico</span>
              </li>
              <li class="nav-item">
                <span href="#" class="nav-link" x-bind:class="{'active' : tab === 'table'}" x-on:click="tab = 'table'">Tabla</span>
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
                <div class="form-group row col-md-5">
                  <label for="" class="col-4 col-form-label">Periodo</label>
                  <select name="" id="" class="form-control col-8" x-model.number="basicPeriod" x-on:change="setPeriod" x-on:input="specificPeriod = 1">
                    <option value="annual">Anual</option>
                    <option value="biannual">Semestral</option>
                    <option value="quarterly">Trimestral</option>
                  </select>
                </div>

                <div class="form-group row col-md-5" x-show.transition="basicPeriod === 'biannual'">
                  <label for="" class="col-5 col-form-label">Semestre</label>
                  <select name="" id="" class="form-control col-7" x-model.number="specificPeriod" x-on:change="setPeriod">
                    <option value="1">Ene - Jun</option>
                    <option value="2">Jul - Dic</option>
                  </select>
                </div>
                
                <div class="form-group row col-md-5" x-show.transition="basicPeriod === 'quarterly'">
                  <label for="" class="col-5 col-form-label">Trimestre</label>
                  <select name="" id="" class="form-control col-7" x-model.number="specificPeriod" x-on:change="setPeriod">
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
      </div>

      <div class="col-lg-6">
        <div class="card" x-data="{tab:'graph'}">
          <div class="card-header mb-2">
            <ul class="nav nav-tabs card-header-tabs">
              <div class="nav-item">
                <li class="nav-link" x-bind:class="{'active' : tab === 'graph'}" x-on:click="tab = 'graph'">Grafico</li>
              </div>
              <div class="nav-item">
                <li class="nav-link" x-bind:class="{'active' : tab === 'table'}" x-on:click="tab = 'table'">Tabla</li>
              </div>
            </ul>
          </div>
          <h3 class="text-center mb-2">Deuda de los Clientes [{{$now->format('Y')}}]</h3>

          <div class="card-body" x-show.transition="tab === 'graph'">
            <canvas id="customersDebts"></canvas>
          </div>

          <div class="card-body table-responsive p-0" style="height: 60vh" x-show.transition="tab === 'table'">
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
                  <td class="text-right">$ {{number_format($creditEvolutions['inititalBalance'], 0, ',', '.')}}</td>
                </tr>
                @foreach ($creditEvolutions['reports'] as $report)
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
          </div>
        </div>
      </div>
    </div>
    <div>
      <div class="card" x-data="{tab:'table'}">
        <div class="card-header mb-2">
          <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
              <span href="#ventasPorCategoría-tabla" class="nav-link" x-bind:class="{'active' : tab === 'table'}" x-on:click="tab = 'table'">Tabla</span>
            </li>
            <li class="nav-item">
              <span href="#ventas-por-categoria-grafica" class="nav-link" x-bind:class="{'active' : tab === 'graph'}" x-on:click="tab = 'graph'">Grafico</span>
            </li>
          </ul>
        </div>
        <h3 class="text-center mb-2">Ventas mensuales por categoría [{{$now->format('Y')}}]</h3>
        <div class="card-body table-responsive p-0" style="height: 60vh" x-show.transition="tab === 'table'">
          <table class="table table-head-fixed table-hover text-nowrap">
            <thead>
              <tr class="text-center">
                <th>Mes</th>
                @foreach ($categories as $id => $category)
                <th>{{$category['name']}}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for ($index = 0; $index < 12; $index++) <tr>
                <td>{{$months[$index]}}</td>
                @foreach ($categories as $id => $category)
                <td class="text-right {{$category['sales'][$index] < $category['average'] ? 'text-danger' : ''}}">$
                  {{number_format($category['sales'][$index], 0, ',', '.')}}</td>
                @endforeach
                </tr>
                @endfor
                <tr class="text-bold">
                  <td>Total:</td>
                  @foreach ($categories as $id => $category)
                  <td class="text-right">$ {{number_format($category['amount'], 0, ',', '.')}}</td>
                  @endforeach
                </tr>
            </tbody>
          </table>
        </div>
        <div class="card-body" x-show.transition="tab === 'graph'" style="max-width: 1080px;">
          <canvas id="salesByCategories"></canvas>
        </div>
      </div>
    </div>
    <!--/.row -->
  </div>
  <!--./container-fluid -->
</div>

@push('scripts')
<script>
  window.monthlyReports = @json($montlyReports);
  window.customersDebts = @json($creditEvolutions);
  window.salesByCategories = @json($categories);
</script>  
<script src="{{asset('assets/pages/js/admin/dashboard.js')}}?v=2.0"></script>  
@endpush