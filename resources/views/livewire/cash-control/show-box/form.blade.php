<div 
  x-data="formComponent()"
  x-init="init($wire, $dispatch, $refs)"
  x-on:enable-form.window="enableForm($event.detail)"
  x-on:disable-form.window="reset"
  x-on:new-cash-register.window="updateRegisterCash($event.detail.cashRegister)"
>
  <form 
    class="card" 
    x-bind:class="{
      'card-dark': state === 'closing-box' || state === 'register',
      'card-info': state === 'upgrade',
    }"
    x-on:submit.prevent="submit"
  >
    <!-- header -->
    <header class="card-header">
      <h5 class="card-title">
        <span x-show="state === 'register'">Registrar Transacción</span>
        <span x-show="state === 'upgrade'">Actualizar Transacción</span>
        <span x-show="state === 'closing-box'">Cierre de caja</span>
      </h5>
    </header><!--/.end header -->
    <!-- body --->
    <div class="card-body">
      <template x-if="state === 'register' || state === 'upgrade'">
        <div>
          @include('livewire.cash-control.show-box.transaction-inputs')
        </div>
      </template>
      <template x-if="state === 'closing-box'" >
        <div>
          @include('livewire.cash-control.show-box.closing-box-inputs')
        </div>
      </template>
  
      {{-- SPINER DE GUARDADO --}}
      <div x-show.transition.duration.200ms="waiting">
        <div class="d-flex align-items-center pt-2 pl-2" >
          <div class="spinner-border text-sm mr-2" role="status">
            <span class="sr-only">Loading...</span>
          </div>
          Guardando datos...
        </div>
      </div>
    </div><!--/end body -->
    <!-- footer -->
    <footer class="card-footer">
      <button type="submit" class="btn" 
        x-bind:class="{
          'btn-dark': state === 'closing-box' || state === 'register',
          'btn-info': state === 'upgrade',
        }"
      >
      <span x-show="state === 'register'">Registrar</span>
      <span x-show="state === 'upgrade'">Actualizar</span>
      <span x-show="state === 'closing-box'">Hacer Cierre</span>
      </button>
      <button type="button" class="btn btn-link" x-on:click="$dispatch('disable-form')">Cancelar</button>
    </footer><!--/.end footer -->
  </form>
</div>