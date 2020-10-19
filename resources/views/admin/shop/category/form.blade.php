<div class="form-group">
  <label>Nombre</label>
  <input 
    type="text" 
    name="permission-name" 
    class="form-control"
    placeholder="Escribelo aquí"
    wire:model.trim.lazy="name"
    autocomplete="off"
    autofocus="autofocus"
  >
  @error('name')
  <div class="text-danger pl-1" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label>Slug</label>
  <input }
    type="text" 
    name="permission-name" 
    class="form-control" 
    placeholder="Escribelo aquí"
    wire:model.lazy="slug"
    autocomplete="off"
  >
  @error('slug')
  <div class="text-danger pl-1" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- Utiliza alpine.js para renderizar el icono --}}
<div class="form-group" x-data="{icon: ''}">
  {{-- Cuando el componente es renderizado se actualiza el valor del icono --}}
  <label>Icono: <i class="{{$icon}}" x-ref="icon"></i></label>

  <input 
    type="text" 
    name="permission-name" 
    class="form-control" 
    placeholder="Escribelo aquí"
    x-on:input="$refs.icon.classList=icon"
    x-model.trim="icon" 
    wire:model.lazy="icon" 
  >
</div>


