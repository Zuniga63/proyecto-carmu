<div class="card card-success">
  <div class="card-header">
    <h2 class="card-title">Registrar nueva categoria</h2>
  </div>
  <div wire:submit.prevent="store" class="form-horizontal">
    <div class="card-body">
      @include('admin.shop.category.form')
      <div wire:loading wire:target="store">
        Realizando el registro...
      </div>
      <div wire:loading wire:target="edit">
        Recuperando informacion...
      </div>
    </div>
    <div class="card-footer">
      <button class="btn btn-success" wire:click="store">Registrar</button>
    </div>
  </div>

</div>