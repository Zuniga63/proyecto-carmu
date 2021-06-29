<div 
  class="card card-dark" 
  x-data="boxComponent()"
  x-init="init($wire, $dispatch, $refs)"
  x-on:box-selected.window="mountBox($event.detail.box)"
  x-on:new-transaction-added.window="addNewTransaction($event.detail.transaction)"
  x-on:transaction-updated.window="updateTransaction($event.detail)"
  x-on:box-closed.window="closingBox = false"
>
  {{-- HEADER CON LOS TABS --}}
  <div class="card-header mb-2">
    <div class="d-flex justify-content-between">
      <ul class="nav nav-tabs card-header-tabs">
        <li class="nav-item">
          <a  
            href="javascript:;" 
            class="nav-link" 
            x-bind:class="{'active' : tab === 'info'}" 
            x-on:click="changeTab('info')"
          >Info</a>
        </li>
        <li class="nav-item">
          <a 
            href="javascript:;"  
            class="nav-link" 
            x-bind:class="{'active' : tab === 'transactions'}" 
            x-on:click="changeTab('transactions')"
          >Movimientos</a>
        </li>
      </ul>
      <div class="text-center" style="margin-top: -5px;">
        <h5 class="text-bold mb-0 text-sm" x-text="box.name"></h5>
        <p class="m-0 text-xs">(<span x-text="box.business"></span>)</p>
      </div>

      <div class="btn btn-danger btn-sm" style="margin-top: -2px" x-on:click="hiddenComponent"><i class="fas fa-times"></i></div>
    </div>
  </div>

  <div class="card-body pt-0">
    <div x-show.transition.in.duration.500ms="tab === 'info' && !closingBox">
      @include('livewire.cash-control.show-box.box-info')
    </div>

    <div x-show.transition.in.duration.500ms="tab === 'info' && closingBox">
      @include('livewire.cash-control.show-box.closing-box')
    </div>

    {{-- TABLA CON LAS TRANSACCIONES --}}
    <div x-show.transition.in.duration.500ms="tab === 'transactions'">
      @include('livewire.cash-control.show-box.box-transactions')
    </div>
    {{-- ARQUEO DE LA CAJA --}}    
    {{-- @include('livewire.cash-control.show-box.closing-box') --}}
  </div>

  <div class="card-footer">
    <div x-show="tab === 'transactions'" style="display: none;">
      <button class="btn btn-primary" x-on:click="enableTransactionForm()">Registrar Transacción</button>
    </div>
    <div x-show="tab === 'info'">
      <button class="btn btn-dark" x-on:click="enableBoxClosing">Hacer Arqueo</button>
    </div>

  </div>
</div>