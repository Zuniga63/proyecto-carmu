<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="form-group">
      <input type="text" name="" id="" class="form-control" placeholder="Buscar" wire:model="search">
    </div>
  </div>
</div>
<div class="row">
  @foreach ($this->customers as $item)
    @include('admin.carmu.customer-profile.basic-profile', compact($item))
  @endforeach
</div>