<div class="form-group">
  <label for="sizeValue" class="required" title="Corresponde a una talla de ropa o zapato" wire:ignore>Valor de la Talla</label>
  <input 
    type="text" 
    name="sizeValue" 
    id="sizeValue" 
    class="form-control {{$errors->has('sizeValue') ? 'is-invalid' : ''}}" 
    placeholder="Ingresa la talla aquí" 
    wire:model="sizeValue"
  >
  @error('sizeValue')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>