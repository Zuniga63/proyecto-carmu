<div class="card card-primary">
  <div class="card-header">
    <h2 class="card-title">Editar menú</h2>
  </div>
  <div class="form-horizontal">
    <div class="card-body">
      @include('admin.menu.form')
      <div wire:loading wire:target="update">
        actualizando los datos...
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