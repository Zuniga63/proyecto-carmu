<div class="card card-primary">
  <div class="card-header">
    <h2 class="card-title">Editar Talla</h2>
  </div>
  <div class="form-horizontal">
    <div class="card-body">
      @include('admin.shop.size.form')
      <div wire:loading wire:target="updateSize">
        actualizando el registro...
      </div>
      <div wire:loading wire:target="edit">
        Recuperando informacion...
      </div>
    </div>
    <div class="card-footer">
      <button class="btn btn-primary" wire:click="updateSize">Actualizar</button>
      <button class="btn btn-link" wire:click="resetFields">Cancelar</button>
    </div>
  </div>

</div>