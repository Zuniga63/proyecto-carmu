<div>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">{{$now->format('Y')}}</h3>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-striped table-hover text-nowrap">
        <thead>
          <tr>
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
            <td>$ {{number_format($report['sales'], 0, ',', '.')}}</td>
            <td>$ {{number_format($report['payments'], 0, ',', '.')}}</td>
            <td>$ {{number_format($report['credits'], 0, ',', '.')}}</td>
            <td class="{{$report['balance'] >= 0 ? 'text-success' : 'text-danger'}}">$ {{number_format($report['balance'], 0, ',', '.')}}</td>
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