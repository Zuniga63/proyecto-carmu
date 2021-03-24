<div class="card" 
  x-bind:class="{
    'card-dark': state === 'creating',
    'card-light': state === 'editing'
  }"
>
  {{-- HEADER CON LOS TABS --}}
  <div class="card-header mb-2">
    <h2 class="card-title">Listado de productos</h2>
  </div>

  <div class="card-body pt-0">
    <div class="table-responsive pt-0" style="height: 60vh">
      <table class="table table-head-fixed table-hover text-nowrap">
        <thead>
          <tr>
            <th>Nombre</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $record)
          <tr>
            <td>{{$record->name}}</td>
            <td class="text-right">
              <a href="javascript:;" class="btn-tools" wire:click="edit({{$record->id}})">
                <i class="fas fa-edit"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>