<div class="card card-primary">
  <div class="card-header">
    <h2 class="card-title">Registrar nuevo usuario</h2>
  </div>
  <form wire:submit.prevent="store">
    <div class="card-body">
      @include('admin.users.form')
    </div>
    <div class="card-footer">
      <button class="btn btn-primary">Registrar</button>
      <button type="button" wire:click="resetFields" class="btn btn-danger float-right">Cancelar</button>
    </div>
  </form>

</div>