<div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Informe de ventas y creditos [{{$now->format('Y')}}]</h3>
          </div>
          <div class="card-body table-responsive p-0" style="height: 60vh">
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
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Evolucion de los creditos [{{$now->format('Y')}}]</h3>
          </div>
          <div class="card-body table-responsive p-0" style="height: 60vh">
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
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Ventas mensuales por categoría [{{$now->format('Y')}}]</h3>
        </div>
        <div class="card-body table-responsive p-0" style="height: 60vh">
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
      </div>
    </div>
    <!--/.row -->
  </div>
  <!--./container-fluid -->
</div>