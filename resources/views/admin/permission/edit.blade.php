<div class="card card-primary">
  <div class="card-header">
    <h2 class="card-title">Editar permiso</h2>
  </div>
  <div class="form-horizontal" id="form-general">
    <div class="card-body">
      @include('admin.permission.form')
    </div>
    <div class="card-footer">
      <button class="btn btn-primary" wire:click="update">Actualizar</button>
      <button class="btn btn-link" wire:click="default">Cancelar</button>
    </div>
  </div>

</div>