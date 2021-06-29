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
        <div id="graphContainer" class="row">
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