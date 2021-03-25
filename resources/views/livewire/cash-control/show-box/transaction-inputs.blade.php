{{-- TIPO DE TRANSACCIÓN --}}
<div class="form-group">
  <label for="transactionType" class="required" >Tipo de Transacción</label>
  <select 
    name="transactionType" 
    id="transactionType" 
    class="form-control {{$errors->has('transactionType') ? 'is-invalid' : ''}}" 
    x-model="transactionType"
  >
    <option value="general">General</option>    
    <option value="sale">Venta</option>    
    <option value="service">Venta de servicio</option>    
    <option value="payment">Abono</option>    
    <option value="expense">Gasto</option>    
    <option value="purchase">Compras</option>    
    <option value="credit">Credito</option>    
  </select>

  @error('categoryId')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- MOMENTO DE LA TRANSACCION--}}
<div class="form-group">
  <label for="moment">¿Cuando ocurre?</label>
  <select name="moment" id="moment" class="form-control" x-model="moment">
    <option value="now">En este momento</option>
    <option value="other">En otra fecha</option>
  </select>
</div>

{{-- FECHA DE LA TRANSACCIÓN --}}
<div class="form-group" x-show.transition.duration.500ms="moment=== 'other'">
  <label for="transactionDate" class="required" title="Si no se establece la hora se registrá al final del día" wire:ignore.self>Selecciona una fecha</label>
  <div class="input-group mb-2">
    <div class="input-group-prepend">
      <span class="input-group-text">
        <i class="far fa-calendar-alt"></i>
      </span>
    </div>
    <input 
      type="date" 
      name="transactionDate" 
      class="form-control {{$errors->has('transactionDate') ? 'is-invalid' : ''}}"
      x-model="transactionDate"
      min="{{$this->minDate}}"
      max="{{$this->maxDate}}"
    >

    @error('transactionDate')
    <div class="invalid-feedback" role="alert">
      {{$message}}
    </div>
    @enderror
  </div>

  <div class="form-check">
    <input type="checkbox" name="setTime" id="setTime" class="form-check-input" x-model="setTime">
    <label for="setTime" class="form-check-label">Establecer hora</label>
  </div>
</div>

{{-- HORA DE LA TRANSACCIÓN --}}
<div class="form-group row" x-show.transition.duration.500ms="setTime && moment === 'other'">
  <label class="col-3 col-form-label" for="transactionTime">Hora:</label>
  <div class="col-9">
    <input 
      type="time" 
      name="transactionTime" 
      id="transactionTime" 
      class="form-control {{$errors->has('transactionTime') ? 'is-invalid' : ''}}" 
      x-model="transactionTime"
    >
    @error('transactionTime')
    <div class="invalid-feedback" role="alert">
      {{$message}}
    </div>
    @enderror
  </div>
</div>

{{-- DESCRIPCIÓN --}}
<div class="form-group">
  <label for="description" class="required d-block text-center">Descripción</label>
  <textarea 
    name="description" 
    id="description" 
    cols="30" 
    class="form-control {{$errors->has('description') ? 'is-invalid' : ''}}"
    placeholder="Describe la transacción"
    x-model="description"
    x-on:focus="$event.target.select()"
  ></textarea>

  @error('description')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- IMPORTE DE LA TRANSACCIÓN --}}
<div class="form-group">
  <label for="transactionAmount" class="required d-block text-center">Importe</label>
  <input 
    type="text" 
    name="transactionAmount" 
    id="transactionAmount" 
    class="form-control text-right text-bold {{$errors->has('transactionAmount') ? 'is-invalid' : ''}}" 
    placeholder="$ 0.00" 
    x-on:input="formatInput($event.target)" 
    x-on:change="$wire.transactionAmount = deleteCurrencyFormat($event.target.value)"
    x-on:focus="$event.target.select()"
    style="font-size: 1.5em;letter-spacing: 2px;"
    autocomplete="off"
  >
  @error('transactionAmount')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group row justify-content-center" x-show.transition.500ms="transactionType === 'general'">
  <div class="col-6">
    <div class="form-check text-center">
      <input id="income" class="form-check-input" type="radio" name="amountType" value="income" x-model="amountType">
      <label class="form-check-label" for="income">Ingreso</label>
    </div>
  </div>
  <div class="col-6">
    <div class="form-check text-center">
      <input id="expense" class="form-check-input" type="radio" name="amountType" value="expense" x-model="amountType">
      <label class="form-check-label" for="expense">Egreso</label>
    </div>
  </div>
</div>