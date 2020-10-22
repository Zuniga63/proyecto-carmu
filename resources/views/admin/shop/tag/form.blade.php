<div class="form-group">
  <label>Nombre</label>
  <input 
    type="text" 
    name="tag_name" 
    class="form-control {{$errors->has('name') ? 'is-invalid' : ''}}"
    placeholder="Escribe el nombre aquí"
    x-model.trim="name"
    x-on:input="slug = name.toLowerCase().replace(/\s/gi, '-').normalize('NFD').replace(/[\u0300-\u036f]/g, '')"
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
    x-model.trim="slug"
    autocomplete="off"
    autofocus="autofocus"
  >
  @error('slug')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>