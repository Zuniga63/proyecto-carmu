<div class="form-group">
  <label for="firstName" class="required">Nombres</label>
  <input 
    id="firstName" 
    type="text" 
    name="name" 
    class="form-control {{$errors->has('firstName') ? 'is-invalid' : ''}}" 
    placeholder="Enrriqueta"
    wire:model.trim.lazy="firstName"
    required
  >
  @error('firstName')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="lastName">Apellidos</label>
  <input 
    id="lastName" 
    type="text" 
    name="lastName" 
    class="form-control {{$errors->has('lastName') ? 'is-invalid' : ''}}" 
    placeholder="Smith Rogers"
    wire:model.trim.lazy="lastName"
  >
  @error('name')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="nit">Nit o CC</label>
  <input 
    id="nit" 
    type="text" 
    name="nit" 
    class="form-control {{$errors->has('nit') ? 'is-invalid' : ''}}" 
    placeholder="Identificación del cliente"
    wire:model.trim.lazy="nit"
  >
  @error('nit')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="phone">Telefono</label>
  <input 
    id="phone" 
    type="text" 
    name="phone" 
    class="form-control {{$errors->has('phone') ? 'is-invalid' : ''}}" 
    placeholder="Escribe el telefono aquí"
    wire:model.trim.lazy="phone"
  >
  @error('phone')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="email">Correo</label>
  <input 
    id="email" 
    type="text" 
    name="email" 
    class="form-control {{$errors->has('email') ? 'is-invalid' : ''}}" 
    placeholder="Escribe el correo aquí"
    wire:model.trim.lazy="email"
  >
  @error('email')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>