<div class="card card-dark">
  <!-- HEADER -->
  <div class="card-header">
    <h5 class="m-0">
      Administración de Cajas
      <i class="fas fa-sync-alt cursor-pointer ml-2 text-lg" title="sincronizar datos" x-on:click="resetComponent"></i>
    </h5>
  </div>
  <!--/end header-->

  <!-- BODY -->
  <div class="card-body overflow-auto scroll-light pb-3" style="height: 75vh;">
    <div x-show="waiting">
      <div class="d-flex flex-column align-items-center">
        <div class="spinner-border mb-3" role="status">
          <span class="sr-only">Loading...</span>
        </div>

        Recuperando información de las cajas...
      </div>
    </div>

    {{-- TARJETAS DE CAJAS --}}
    <div x-show.transition.in.duration.300ms="!waiting" style="display: none">
      <template x-for="box in boxs" x-bind:key="box.id">
        <!-- box card -->
        <div 
          class="card cursor-pointer"
          x-bind:class="{'card-secondary': !box.main, 'card-primary': box.main}"
          x-on:click.stop="selectBox(box)"
        >
          <!-- HEADER -->
          <div class="card-header text-center p-2 position-relative" >
            <h6 class="text-bold mb-0" x-text="box.name"></h6>
            <p class="m-0 text-xs">(<span x-text="box.business"></span>)</p>
            <i class="far fa-check-circle position-absolute" x-show.transition.durations.300ms="box.selected" style="top: 10px; right: 20px;"></i>
          </div>
          <!--/end HEADER -->

          <!-- BODY -->
          <div class="card-body p-2">
            <p class="m-0 text-xs">Arqueo: <span class="text-bold" x-text="box.closingDateFormatted"></span> (<span x-text="box.closingDateRelative"></span>)</p>
            <p class="m-0 text-xs">Base: <span class="text-bold" x-text="formatCurrency(box.base, 0)"></span> </p>
            <p 
              class=" col-12 text-bold text-center mb-0 mt-2" 
              style="font-size: 1.5em;opacity: 0.8;" 
              x-text="formatCurrency(box.balance, 0)"
            ></p>
            <div class="d-flex justify-content-between text-sm">
              <p class="m-0">Ingresos: <span class="text-bold" x-text="formatCurrency(box.totalIncomes, 0)"></span></p>
              <p class="m-0">Egresos: <span  class="text-bold"x-text="formatCurrency(box.totalExpenses, 0)"></span></p>
            </div>
          </div>

        </div>
        <!--/end card-->
      </template>
      
    </div>

  </div>
</div>
