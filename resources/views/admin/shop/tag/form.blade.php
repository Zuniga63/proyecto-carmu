<div class="form-group">
  <label>Nombre</label>
  <input 
    type="text" 
    name="tag_name" 
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

<div class="form-group">
  <label>Slug</label>
  <input 
    type="text" 
    name="tag_name" 
    class="form-control {{$errors->has('slug') ? 'is-invalid' : ''}}"
    placeholder="Escribe el slug"
    wire:model.trim="slug"
    autocomplete="off"
    autofocus="autofocus"
  >
  @error('slug')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>