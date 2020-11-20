<div class="card card-info" style="max-height: 70vh;">
  <div class="card-header">
    <div class="card-title">Historial de Creditos y Pagos</div>
  </div>
  <div class="card-body table-responsive p-0" style="max-height: 70vh;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap text-center">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Credito</th>
          <th>Abono</th>
          <th>Deuda</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($history as $record)
        <tr class="{{$customer->archived ? 'text-muted' : ''}}">
          <td>{{$this->formatDateWithFormat($record->date, 'Y-m-d', 'D-MM-YY') }}</td>
          <td class="text-right">
            {{$record->credit ?  '$ ' . number_format($record->credit, 0, '.' , ' ') : ''}}
          </td>
          <td class="text-right">
            {{$record->payment ? '$ ' . number_format($record->payment, 0, '.' , ' ') : ''}}
          </td>
          <td class="text-right">
            {{'$ ' . number_format($record->debt, 0, '.' , ' ')}}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>