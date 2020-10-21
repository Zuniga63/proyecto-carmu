<div class="card card-primary">
  <div class="card-header">
    <h2 class="card-title">Editar Etiqueta</h2>
  </div>
  <div class="form-horizontal">
    <div class="card-body">
      @include('admin.shop.tag.form')
      <div wire:loading wire:target="store">
        Realizando el registro...
      </div>
      <div wire:loading wire:target="edit">
        Recuperando informacion...
      </div>
    </div>
    <div class="card-footer">
      <button class="btn btn-primary" wire:click="update">Editar</button>
      <button class="btn btn-link" wire:click="resetFields">Cancelar</button>
    </div>
  </div>

</div>