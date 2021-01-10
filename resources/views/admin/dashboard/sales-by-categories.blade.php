<div class="card" x-data="{tab:'table'}">
  <div class="card-header mb-2">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'table'}" x-on:click="tab = 'table'">Tabla</a>
      </li>
      <li class="nav-item">
        <a href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'graph'}" x-on:click="tab = 'graph'">Grafico</a>
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