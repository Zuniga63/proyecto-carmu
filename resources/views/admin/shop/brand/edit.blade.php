<div class="card card-primary">
  <div class="card-header">
    <h2 class="card-title">Editar Marca</h2>
  </div>
  <div class="form-horizontal">
    <div class="card-body">
      @include('admin.shop.brand.form')
      <div wire:loading wire:target="store">
        actualizando el registro...
      </div>
      <div wire:loading wire:target="edit">
        Recuperando informacion...
      </div>
    </div>
    <div class="card-footer">
      <button class="btn btn-primary" wire:click="update">Actualizar</button>
      <button class="btn btn-link" wire:click="resetFields">Cancelar</button>
    </div>
  </div>

</div>