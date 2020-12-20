<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="form-group">
      <input type="text" name="" id="" class="form-control" placeholder="Buscar" wire:model="search">
    </div>

    <div class="d-flex justify-content-around mb-4" x-data="{listinType:@entangle('listingType')}">
      <div class="form-check">
        <input type="radio" name="" id="activeOption" class="form-check-input" value="active" x-model="listinType">
        <label for="activeOption" class="form-check-label">Clientes Activos</label>
      </div>
      <div class="form-check">
        <input type="radio" name="" id="archivedOption" class="form-check-input" value="archived" x-model="listinType">
        <label for="archivedOption" class="form-check-label">Clientes Archivados</label>
      </div>
    </div>
  </div>
</div>
<div class="row overflow-auto border rounded p-2 p-xl-4" style="max-height: 65vh">
  {{-- @dump($this->customers) --}}
  @foreach ($this->customersList as $item)
    @include('admin.carmu.customer-profile.basic-profile', compact('item'))
  @endforeach
</div>