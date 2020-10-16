<div class="card card-success">
  <div class="card-header">
    <h2 class="card-title">Nuevo permiso</h2>
  </div>
  <div class="form-horizontal" id="form-general">
    <div class="card-body">
      @include('admin.permission.form')
    </div>
    <div class="card-footer">
      <button class="btn btn-success" wire:click="store">Crear</button>
    </div>
  </div>

</div>