{{-- IMPORTE DE LA TRANSACCIÓN --}}
<div class="form-group">
  <label for="newBase" class="required d-block text-center">Nueva Base</label>
  <input 
    type="text" 
    name="newBase" 
    id="newBase" 
    class="form-control text-right text-bold {{$errors->has('newBase') ? 'is-invalid' : ''}}" 
    placeholder="$ 0.00" 
    x-on:input="formatInput($event.target)" 
    x-on:change="$wire.newBase = deleteCurrencyFormat($event.target.value)"
    style="font-size: 1.5em;letter-spacing: 2px;"
    autocomplete="off"
  >
  @error('newBase')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>
