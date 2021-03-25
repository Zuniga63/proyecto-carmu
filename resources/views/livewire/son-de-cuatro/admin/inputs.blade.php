<div class="form-group">
  <label for="name" class="required">Nombre</label>
  <input 
    id="name" 
    type="text" 
    name="name" 
    class="form-control {{$errors->has('name') ? 'is-invalid' : ''}}" 
    placeholder="Escribe el nombre aquí"
    wire:model.defer="name"
    required
  >
  @error('name')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- IMPORTE DE LA TRANSACCIÓN --}}
<div class="form-group">
  <label for="expense" class="required d-block">Costo</label>
  <input 
    type="text" 
    name="expense" 
    id="expense" 
    class="form-control text-right text-bold {{$errors->has('expense') ? 'is-invalid' : ''}}" 
    placeholder="$ 0.00" 
    x-on:input="formatInput($event.target)" 
    x-on:change="$wire.expense = deleteCurrencyFormat($event.target.value)"
    x-on:focus="$event.target.select()"
    style="font-size: 1.5em;letter-spacing: 2px;"
    autocomplete="off"
  >
  @error('expense')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- IMPORTE DE LA TRANSACCIÓN --}}
<div class="form-group">
  <label for="price" class="required d-block">Precio</label>
  <input 
    type="text" 
    name="price" 
    id="price" 
    class="form-control text-right text-bold {{$errors->has('price') ? 'is-invalid' : ''}}" 
    placeholder="$ 0.00" 
    x-on:input="formatInput($event.target)" 
    x-on:change="$wire.price = deleteCurrencyFormat($event.target.value)"
    x-on:focus="$event.target.select()"
    style="font-size: 1.5em;letter-spacing: 2px;"
    autocomplete="off"
  >
  @error('price')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- IMAGEN DEL PRODCUTO --}}
<div class="container mb-3">
  <div class="row justify-content-around align-items-center mb-2">
    <img src="{{ $this->imagePath }}" alt="" class="img-thumbnail d-block mx-auto col-5">
    {{-- Imagen actual en el servidor --}}
  </div>

  @if ($state === 'creating')
  <div class="row justify-content-center">
    <progress x-show.transition="isUploading" max="100" x-bind:value="progress" class="col-6"></progress>
    @error('image')
    <div class="text-danger border border-danger rounded px-2 mb-2" role="alert">
      {{$message}}
    </div>
    @enderror
  </div>

  <div class="row justify-content-around">
    <label class="btn btn-primary mb-0">
      <i class="fas fa-cloud-upload-alt"></i> Subir imagen
      <input type="file" accept="image/*" wire:model="image" class="d-none">
    </label>
  </div>
  @endif
</div>