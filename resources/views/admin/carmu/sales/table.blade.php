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
    @foreach ($this->sales as $sale)
    <tr>
      <td class="text-center">{{$sale['id']}}</td>
      <td class="text-center">{{$sale['date']}}</td>
      <td>{{$sale['description']}}</td>
      <td class="text-center">$ {{number_format($sale['amount'], 0, ',', '.')}}</td>
      <td class="pr-0">
        <div class="btn-group p-0">
          <button 
            class="btn btn-info" title="Editar" 
            data-toggle="tooltip" 
            data-placement="top" 
            wire:click="edit({{$sale['id']}})"
          >
            <i class="fas fa-edit"></i>
          </button>
          <button 
            class="btn btn-danger" 
            title="Eliminar"
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
    @endforeach
  </tbody>
</table>