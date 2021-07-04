<div 
  class="container-fluid pt-3" 
  x-data="app()" 
  x-init="init($wire, $dispatch)"
  x-on:hidden-box-view="hiddenBoxView"
  x-on:enable-form="formActive = true"
  x-on:disable-form="formActive = false"
  x-on:new-transaction="addNewTransaction($event.detail)"
  x-on:update-transaction="updateTransaction($event.detail)"
  x-on:box-closed="resetComponent"
  wire:ignore
>
    <div class="row">
      {{-- PANEL CON LAS CAJAS --}}
      <div class="col-12 col-lg-4" x-show.transition.in.duration.200ms="!formActive">
        @include('livewire.cash-control.show-box.sidebar')
      </div>

      {{-- FORMULARIO DE CIERRE Y TRANSACCIONES --}}
      <div class="col-12 col-lg-4" x-show.transition.in.duration.200ms="formActive" style="display: none;">
        @include('livewire.cash-control.show-box.form')
      </div>

      {{-- GRAFICAS DEL NEGOCIO --}}
      <div 
        class="col-12 col-lg-8"
        x-show.transition.in.duration.300ms="!boxActive"
      >
        <div class="card card-light">
          <header class="card-header">
            <h5 class="text-center mb-0">Estadisticas</h5>
          </header>
          <div class="card-body">
            <!-- SELECTORES -->
            <div class="row">
              <!-- Selección del negocio -->
              <div class="col-6">
                <div class="form-group row">
                  <label for="business" class="col-4">Negocio</label>
                  <select name="business" id="business" class="form-control col-8" x-model.number="businessSelected" x-on:change="updateStatistics">
                    <option x-bind:value="0" selected disabled>Selecciona un negocio</option>
                    <template x-for="item in business" x-bin:key="item.id">
                      <option x-bind:value="item.id" x-text="item.name"></option>
                    </template>
                  </select>
                </div>
              </div>
              <!-- Selección del periodo -->
              <div class="col-6">
                <div class="form-group row">
                  <label for="business" class="col-4">Periodo</label>
                  <select name="business" id="business" class="form-control col-8" x-model="graphPeriod" x-on:change="updateStatistics">
                    <option value="this-month">Este mes</option>
                    <option value="last-month">Mes Anterior</option>
                    <option value="all-months">Todos los meses</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <!-- Ingresos, Egresos y Saldo -->
              <div class="col-12" id="generalGraphContainer">
                <canvas id="generalGraph"></canvas>
              </div>
              <!-- Ingresos por categorías -->
              <div class="col-6" id="incomesGraphContainer">
                <canvas id="incomesGraph"></canvas>
              </div>

              <!-- Egresos por categorías -->
              <div class="col-6" id="expensesGraphContainer">
                <canvas id="expensesGraph"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- PANEL DE CONTROL CAJAS --}}
      <div 
        class="col-12 col-lg-8" 
        x-show.transition.in.duration.200ms="boxActive && boxSelected" 
        style="display: none;"
      >
        @include('livewire.cash-control.show-box.box-panel')
      </div>
    </div>
</div>

@push('scripts')
<script src="{{mix('js/admin/show-box/app.js')}}" defer></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    OverlayScrollbars(document.querySelectorAll('.scroll-light'), {
      className: "os-theme-dark",
      resize: "none",
      sizeAutoCapable: true,
      paddingAbsolute: true,
      scrollbars: {
        clickScrolling: true
      }
    });
  });
</script>

@endpush

@push('styles')
  <style>
    .cursor-pointer{
      cursor: pointer;
    }
  </style>
@endpush