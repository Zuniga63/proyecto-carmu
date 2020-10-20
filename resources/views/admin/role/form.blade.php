<div class="form-group">
  <label>Nombre</label>
  <input 
    type="text" 
    name="permission-name" 
    class="form-control {{$errors->has('name') ? 'is-invalid' : ''}}"
    placeholder="Escribe el nombre aquí"
    wire:model.trim="name"
    autocomplete="off"
    autofocus="autofocus"
  >
  @error('name')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>