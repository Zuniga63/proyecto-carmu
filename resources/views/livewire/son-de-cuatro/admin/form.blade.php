<form 
  class="card" 
  x-bind:class="{
    'card-dark': state === 'creating',
    'card-light': state === 'editing'
  }"
  wire:submit.prevent="submit"
>
  <!-- header -->
  <header class="card-header">
    <h5 class="card-title" x-show="state === 'creating'">Registrar Producto</h5>
    <h5 class="card-title" x-show="state === 'editing'">Actualizar Producto</h5>
  </header><!--/.end header -->
  <!-- body --->
  <div class="card-body">
    @include('livewire.son-de-cuatro.admin.inputs')
    <div wire:loading wire:target="submit">
      Haciendo solicitud...
    </div>
  </div><!--/end body -->
  <!-- footer -->
  <footer class="card-footer">
    <button type="submit" class="btn" 
      x-bind:class="{
        'btn-dark': state === 'creating',
        'btn-success': state === 'editing'
      }"
    >
      <span x-show="state === 'creating'">Registrar</span>
      <span x-show="state === 'editing'">Actualizar</span>
    </button>
    <button type="button" class="btn btn-link" wire:click="resetFields">Cancelar</button>
  </footer><!--/.end footer -->
</form>