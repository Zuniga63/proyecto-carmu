<div class="card card-primary">
  <div class="card-header">
    <h2 class="card-title">Editar categoría</h2>
  </div>
  <div class="form-horizontal">
    <div class="card-body">
      @include('admin.shop.category.form')
      <div wire:loading wire:target="update">
        actualizando los datos...
      </div>
    </div>
    <div class="card-footer">
      <button class="btn btn-primary" wire:click="update">Actualizar</button>
      <button class="btn btn-link" wire:click="resetFields">Cancelar</button>
    </div>
  </div>

</div>