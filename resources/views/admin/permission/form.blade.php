<div class="form-group">
  <label>Nombre</label>
  <input type="text" name="permission-name" class="form-control" id="name" wire:model="name" required>
  @error('name')
  <span>{{$message}}</span>
  @enderror
</div>
