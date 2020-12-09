<div class="card card-primary" style="max-height: 70vh; min-height: 40vh;" x-data="{type:'pending'}">
  <div class="card-header">
    <div class="card-title float-none text-center">Créditos</div>
    <div class="d-flex justify-content-around">
      <div class="form-group mb-0">
        <input type="radio" id="paid" class="form-input" x-model="type" value="paid">
        <label for="paid" class=" mb-0">Pagados</label>
      </div>
      <div class="form-group mb-0">
        <input type="radio" id="pending" class="form-input" x-model="type" value="pending">
        <label for="pending" class=" mb-0">Pendientes</label>
      </div>
    </div>
  </div>
  {{-- TABLA CON LOS CREDITOS PENDIENTES --}}
  <div class="card-body table-responsive p-0" x-show="type === 'pending'">
    <table class="table table-head-fixed table-hover table-striped text-nowrap text-center">
      <thead>
        <tr>
          <th>#</th>
          <th>Fecha</th>
          <th>Venc.</th>
          <th>Descripción</th>
          <th>Saldo</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($customer['pendingCredits'] as $key => $credit)
        <tr>
          <td>{{$key + 1}}</td>
          <td class="text-center">
            {{$credit['date']}}
          </td>
          <td class="text-center">
            {{$credit['expiration']}}
            {{-- {{$credit->expiration->format('d-m-Y H:i:s')}} --}}
          </td>
          <td class="text-left">{{$credit['description']}}</td>
          <td class="text-right">
            $ {{$credit['balance'] ? number_format($credit['balance'], 0, '.' , ' ') : ''}}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="card-body table-responsive p-0" x-show="type === 'paid'">
    <table class="table table-head-fixed table-hover table-striped text-nowrap text-center">
      <thead>
        <tr>
          <th>#</th>
          <th>Fecha</th>
          <th>Pagado</th>
          <th>Duración</th>
          <th>Descripción</th>
          <th>Valor</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($customer['creditsPaid'] as $key => $credit)
        <tr>
          <td>{{$key + 1}}</td>
          <td>{{$credit['date']}}</td>
          <td class="text-center">
            {{$credit['paymentDate']}}
          </td>
          <td class="text-center">
            {{$credit['duration']}}
          </td>
          <td class="text-left">{{$credit['description']}}</td>
          <td class="text-right">
            $ {{$credit['amount'] ? number_format($credit['amount'], 0, '.' , ' ') : ''}}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>