<div class="form-group">
  <label for="saleMoment">Momento de la venta</label>
  <select name="saleMoment" id="transactionDate" class="form-control">
    <option value="now">En este momento</option>
    <option value="other">En otra fecha</option>
  </select>
</div>

<div class="form-group">
  <label for="saleMoment">Selecciona una fecha</label>
  <div class="input-group" x-show.transition="moment === 'other'">
    <div class="input-group-prepend">
      <span class="input-group-text">
        <i class="far fa-calendar-alt"></i>
      </span>
    </div>
    <input type="date" name="transactionDate" class="form-control {{$errors->has('saleMoment') ? 'is-invalid' : ''}}"
      {{-- x-model="date"  --}} {{-- min="{{$this->minDate}}" --}} {{-- max="{{$this->maxDate}}" --}}>

    @error('saleMoment')
    <div class="invalid-feedback" role="alert">
      {{$message}}
    </div>
    @enderror
  </div>
</div>

<div class="form-group">
  <label for="saleCategory" class="required">Categoría</label>
  <select 
    name="saleCategory" 
    id="saleCategory"
    class="form-control {{$errors->has('saleCategory') ? 'is-invalid' : ''}}"
  >
    <option value="" selected disabled>Selecciona una</option>
    <option value="now">Categoría 1</option>
    <option value="other">Categoría 2</option>
  </select>

  @error('saleCategory')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="saleDescription" class="required">Descripción</label>
  <textarea 
    name="saleDescription" 
    id="saleDescription" 
    cols="30" 
    class="form-control {{$errors->has('saleDescription') ? 'is-invalid' : ''}}"
    placeholder="Escribe los detalles de la venta"
  ></textarea>

  @error('saleDescription')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label for="saleAmount" wire:ignore class="required">Importe de la venta</label>
  <input 
    type="text" 
    name="saleAmount" 
    id="saleAmount" 
    class="form-control text-right {{$errors->has('saleAmount') ? 'is-invalid' : ''}}" 
    placeholder="$ 0.00" 
    {{-- x-on:input="formatInput($event.target)"  --}}
    {{-- x-on:change="$wire.transactionAmount = deleteCurrencyFormat($event.target.value)" --}}
  >
  @error('saleAmount')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>