<div class="form-group">
  <label>Nombre</label>
  <input type="text" name="permission-name" class="form-control" id="name" wire:model.lazy="name" required placeholder="{{$placeholder}}">
  @error('name')
  <span>{{$message}}</span>
  @enderror
</div>
