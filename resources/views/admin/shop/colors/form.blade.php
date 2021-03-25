<div class="form-group">
  <label 
    for="colorName" 
    class="required" 
    title="Nombre unico de un color para relacionar" 
    wire:ignore
  >
    Nombre
  </label>
  <input 
    type="text" 
    name="colorName" 
    id="colorName" 
    class="form-control {{$errors->has('colorName') ? 'is-invalid' : ''}}" 
    placeholder="Escribe el nombre del color aquí" 
    wire:model="colorName"
    required
  >
  @error('colorName')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label 
    for="colorHex" 
    class="required" 
    title="Codigo de color usado para representar productos" 
    wire:ignore
  >
    Codigo de color
  </label>
  <input 
    type="color" 
    name="colorHex" 
    id="colorHex" 
    class="form-control {{$errors->has('colorHex') ? 'is-invalid' : ''}}" 
    wire:model="colorHex"
    required
  >
  @error('colorHex')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>