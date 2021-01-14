<div class="card" x-data="{tab:'graph'}">
  <div class="card-header mb-2">
    <ul class="nav nav-tabs card-header-tabs">
      <div class="nav-item">
        <a href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'graph'}" x-on:click="tab = 'graph'">Grafico</a>
      </div>
      <div class="nav-item">
        <a href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'table'}" x-on:click="tab = 'table'">Tabla</a>
      </div>
    </ul>
  </div>
  <h3 class="text-center mb-2">Deuda de los Clientes [{{$now->format('Y')}}]</h3>

  <div class="card-body" x-show.transition="tab === 'graph'">
    <canvas id="customersDebts" wire:ignore></canvas>
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