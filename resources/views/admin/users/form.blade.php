<div class="form-group">
  <label for="name" class="required">Nombre</label>
  <input 
    id="name" 
    type="text" 
    name="name" 
    class="form-control {{$errors->has('name') ? 'is-invalid' : ''}}" 
    placeholder="Nombre y Apellido"
    wire:model.trim.lazy="name"
    required
    >
    @error('name')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="email" class="required">Email</label>
  <input 
    id="email" 
    type="email" 
    name="email" 
    wire:model.trim.lazy="email"
    class="form-control {{$errors->has('email') ? 'is-invalid' : ''}}" 
    placeholder="ejemplo@gmail.com"
    required
  >
  @error('email')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="password" class="required">Contraseña</label>
  <input 
    id="password" 
    type="password" 
    name="password" 
    wire:model.lazy="password"
    class="form-control {{$errors->has('password') ? 'is-invalid' : ''}}" 
    required
  >
  @error('password')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="confirmPassword" class="required">Confirmar Contraseña</label>
  <input 
    id="confirmPassword" 
    type="password" 
    name="confirm_password" 
    wire:model.lazy="password_confirmation"
    class="form-control {{$errors->has('password_confirmation') ? 'is-invalid' : ''}}" 
    required
  >
  @error('password_confirmation')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="userRol">Rol del usuario</label>
  <select name="categoryId" id="userRol" class="form-control" wire:model.number="rolId">
    <option value="0">Sin rol</option>
    @foreach ($roles as $id => $roleName)
    <option value="{{$id}}">{{$roleName}}</option>
    @endforeach
  </select>
</div>