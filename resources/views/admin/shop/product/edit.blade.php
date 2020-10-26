<div class="card card-info">
  <div class="card-header">
    <h2 class="card-title">Registrar Producto</h2>
  </div>
  <form wire:submit.prevent="update">
    <div class="card-body">
      @include('admin.shop.product.form-general')
      @include('admin.shop.product.form-published')
    </div>
    <div class="card-footer">
      <button class="btn btn-primary">Actualizar</button>
      <button type="button" wire:click="resetFields" class="btn btn-danger float-right">Cancelar</button>
    </div>
  </form>

</div>