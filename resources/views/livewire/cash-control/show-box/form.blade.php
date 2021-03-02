<form class="card-dark border" wire:submit.prevent="submit">
  <!-- header -->
  <header class="card-header">
    <h5 class="card-title">Registrar Transacción</h5>
  </header><!--/.end header -->
  <!-- body --->
  <div class="card-body">
    <div x-show="tab === 'transactions'">
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
    <button type="submit" class="btn btn-dark">Registrar</button>
    <button type="button" class="btn btn-link" wire:click="resetField">Cancelar</button>
  </footer><!--/.end footer -->
</form>