<form 
  class="card" 
  x-bind:class="{
    'card-dark': state === 'registering',
    'card-light': state === 'editing'
  }"
  wire:submit.prevent="submit"
>
  <!-- header -->
  <header class="card-header">
    <h5 class="card-title" x-show="tab === 'transactions' && state === 'registering' && !closingBox">Registrar Transacción</h5>
    <h5 class="card-title" x-show="tab === 'transactions' && state === 'editing' && !closingBox">Actualizar Transacción</h5>
    <h5 class="card-title" x-show="closingBox">Cierre de caja</h5>
  </header><!--/.end header -->
  <!-- body --->
  <div class="card-body">
    <div x-show="tab === 'transactions' && !closingBox">
      @include('livewire.cash-control.show-box.transaction-inputs')
    </div>
    <div x-show="closingBox">
      @include('livewire.cash-control.show-box.closing-box-inputs')
    </div>

    <div wire:loading wire:target="submit">
      Haciendo solicitud...
    </div>
  </div><!--/end body -->
  <!-- footer -->
  <footer class="card-footer">
    <button type="submit" class="btn" 
      x-bind:class="{
        'btn-dark': state === 'registering',
        'btn-success': state === 'editing'
      }"
    >
      <span x-show="state === 'registering'">Registrar</span>
      <span x-show="state === 'editing'">Actualizar</span>
    </button>
    <button type="button" class="btn btn-link" wire:click="resetFields">Cancelar</button>
  </footer><!--/.end footer -->
</form>