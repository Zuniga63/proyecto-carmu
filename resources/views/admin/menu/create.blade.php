<div class="card card-success">
  <div class="card-header">
    <h2 class="card-title">Registrar un nuevo menú</h2>
  </div>
  <div class="form-horizontal">
    <div class="card-body">
      @include('admin.menu.form')
      {{-- <div wire:loading wire:target="store"> --}}
        {{-- Realizando el registro... --}}
      {{-- </div> --}}
    </div>
    <div class="card-footer">
      <button class="btn btn-success" wire:click="store">Registrar</button>
    </div>
  </div>

</div>