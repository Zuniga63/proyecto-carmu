<div class="card card-info">
  <div class="card-header">
    <h2 class="card-title">Editar Venta</h2>
  </div>
  <form wire:submit.prevent="update" x-data="formModel()">
    <div class="card-body">
      @include('admin.carmu.sales.form')
      <div wire:loading wire:target="update">
        Procesando venta...
      </div>
      <div wire:loading wire:target="edit">
        Cargando datos...
      </div>
    </div>
    <div class="card-footer">
      <button class="btn btn-info">Actualizar</button>
      <button type="button" wire:click="resetFields" class="btn btn-danger float-right">Cancelar</button>
    </div>
  </form>

</div>