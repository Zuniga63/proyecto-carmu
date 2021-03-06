<div class="card" 
  x-bind:class="{
    'card-dark': state === 'registering',
    'card-light': state === 'editing'
  }"
>
  {{-- HEADER CON LOS TABS --}}
  <div class="card-header mb-2">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a  
          href="javascript:;" 
          class="nav-link" 
          x-bind:class="{'active' : tab === 'info'}" 
          x-on:click="tab = 'info'"
        >Info</a>
      </li>
      <li class="nav-item">
        <a 
          href="javascript:;"  
          class="nav-link" 
          x-bind:class="{'active' : tab === 'transactions'}" 
          x-on:click="tab = 'transactions'"
        >Movimientos</a>
      </li>
      <li class="nav-item">
        <a  
          href="javascript:;" 
          class="nav-link disabled" 
          x-bind:class="{'active' : tab === 'graph'}" 
        >Graficos</a>
      </li>
    </ul>
  </div>

  <div class="card-body pt-0">
    <h3 class="text-center text-bold mb-0">{{$box['name']}}</h3>
    <p class="text-center border-bottom" x-bind:class="{'mb-0': tab==='transactions'}">
      {{$box['business']}} <a href="javascript:;" class="link" wire:click="render"><i class="fas fa-sync-alt"></i></a>
    </p>
    {{-- Informacion de la caja --}}
    <div x-show.transition.in.duration.500ms="tab === 'info'">
      @include('livewire.cash-control.show-box.box-info')
    </div>
    {{-- TABLA CON LAS TRANSACCIONES --}}
    <div class="table-responsive pt-0" style="height: 60vh" x-show.transition.in.duration.500ms="tab === 'transactions'">
      <table class="table table-head-fixed table-hover text-nowrap">
        <thead>
          <tr class="text-center">
            <th>Fecha</th>
            <th class="text-left">Descripción</th>
            <th>Importe</th>
            <th>Saldo</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($this->transactions as $record)
          <tr>
            <td class="text-center">{{$record['date']}}</td>
            <td class="text-left">{{$record['description']}}</td>
            <td class="text-right" x-text="formatCurrency({{$record['amount']}},0)"></td>
            <td class="text-right" x-text="formatCurrency({{$record['balance']}},0)"></td>
            <td>
              <a href="javascript:;" class="btn-tools" wire:click="editTransaction({{$record['id']}})">
                <i class="fas fa-edit"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{-- ARQUEO DE LA CAJA --}}    
    @include('livewire.cash-control.show-box.closing-box')
  </div>
</div>