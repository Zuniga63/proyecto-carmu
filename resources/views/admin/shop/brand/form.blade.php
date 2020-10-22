<div class="form-group">
  <label>Nombre</label>
  <input 
    type="text" 
    name="brand_name" 
    class="form-control {{$errors->has('name') ? 'is-invalid' : ''}}"
    placeholder="Nombre de la marca"
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
    name="brand_name" 
    class="form-control {{$errors->has('slug') ? 'is-invalid' : ''}}"
    placeholder="slug-de-la-marca"
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