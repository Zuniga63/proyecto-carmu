<div class="card card-info">
  <div class="card-header">
    <h2 class="card-title">Editar Cliente</h2>
  </div>
  <form wire:submit.prevent="update">
    <div class="card-body">
      @include('admin.carmu.customers.form')
    </div>
    <div class="card-footer">
      <button class="btn btn-primary">Actualizar</button>
      <button type="button" wire:click="resetFields" class="btn btn-danger float-right">Descartar Cambios</button>
    </div>
  </form>

</div>