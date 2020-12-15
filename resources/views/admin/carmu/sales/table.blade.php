<table class="table table-head-fixed table-hover text-nowrap">
  <thead>
    <tr class="text-center">
      <th>ID</th>
      <th>Fecha</th>
      <th class="text-left">Descripción</th>
      <th>Importe</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="text-center">1</td>
      <td class="text-center">12-01-2020</td>
      <td>Primera venta del sistema - detalle largo</td>
      <td class="text-center">$ 1.200.000</td>
      <td class="pr-0">
        <div class="btn-group p-0">
          <button 
            class="btn btn-info" title="Editar Cliente" 
            data-toggle="tooltip" 
            data-placement="top" 
            {{-- wire:click="edit({{$customer->customer_id}})" --}}
          >
            <i class="fas fa-edit"></i>
          </button>
          <button 
            class="btn btn-danger" 
            title="Eliminar Cliente"
            data-placement="top"
            data-toggle="modal" data-target="#deleteModal"
            {{-- {{$customer->balance > 0 ? 'disabled' : ''}} --}}
            {{-- wire:click="$emit('save-id', {{$customer->customer_id}} )" --}}
          >
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </td>
    </tr>
    <tr>
      <td class="text-center">1</td>
      <td class="text-center">12-01-2020</td>
      <td>Primera venta del sistema - detalle largo</td>
      <td class="text-center">$ 1.200.000</td>
      <td class="pr-0">
        <div class="btn-group p-0">
          <button 
            class="btn btn-info" title="Editar Cliente" 
            data-toggle="tooltip" 
            data-placement="top" 
            {{-- wire:click="edit({{$customer->customer_id}})" --}}
          >
            <i class="fas fa-edit"></i>
          </button>
          <button 
            class="btn btn-danger" 
            title="Eliminar Cliente"
            data-placement="top"
            data-toggle="modal" data-target="#deleteModal"
            {{-- {{$customer->balance > 0 ? 'disabled' : ''}} --}}
            {{-- wire:click="$emit('save-id', {{$customer->customer_id}} )" --}}
          >
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </td>
    </tr>
    {{-- <tr>
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
    @endforeach --}}
  </tbody>
</table>