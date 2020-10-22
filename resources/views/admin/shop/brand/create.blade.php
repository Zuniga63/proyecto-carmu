<div class="card card-info">
  <div class="card-header">
    <h2 class="card-title">Registrar Marca</h2>
  </div>
  <div class="form-horizontal">
    <div class="card-body">
      @include('admin.shop.brand.form')
      <div wire:loading wire:target="store">
        Realizando el registro...
      </div>
      <div wire:loading wire:target="edit">
        Recuperando informacion...
      </div>
    </div>
    <div class="card-footer">
      <button class="btn btn-info" wire:click="store">Registrar</button>
    </div>
  </div>

</div>