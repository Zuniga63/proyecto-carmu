{{-- TIPO DE TRANSACCIÓN --}}
<div class="form-group">
  <label 
    x-bind:for="transactionType.id" 
    x-bind:class="{'required': transactionType.required}" 
    x-html="transactionType.label"
  ></label>
  <select 
    x-bind:id="transactionType.id" 
    x-bind:name="transactionType.name" 
    class="form-control {{$errors->has('transactionType') ? 'is-invalid' : ''}}" 
    x-bind:class="{'is-invalid': transactionType.hasError}"
    x-model="transactionType.value"
    x-bind:required="transactionDate.required"
    x-bind:disabled="waiting"
    x-on:change="validateTransactionType"
  >
    <option value="general">General</option>    
    <option value="sale">Venta</option>    
    <option value="service">Venta de servicio</option>    
    <option value="payment">Abono</option>    
    <option value="expense">Gasto</option>    
    <option value="purchase">Compras</option>    
    <option value="credit">Credito</option>    
  </select>
  
  <div x-show="transactionType.hasError" class="invalid-feedback" role="alert">
    <span x-text="transactionType.errorMessage"></span>
  </div>
</div>

{{-- MOMENTO DE LA TRANSACCION--}}
<div class="form-group">
  <label x-bind:for="moment.id" x-text="moment.label" x-bind:class="{'required': moment.required}"></label>
  <select 
    x-bind:name="moment.name" 
    x-bind:id="moment.id" 
    class="form-control" 
    x-bind:class="{'is-invalid': moment.hasError}"
    x-model="moment.value"
    x-bind:required="moment.required"
    x-bind:disabled="waiting"
    x-on:change="validateMoment"
  >
    <option value="now">En este momento</option>
    <option value="other">En otra fecha</option>
  </select>

  <div x-show="moment.hasError" class="invalid-feedback" role="alert">
    <span x-text="moment.errorMessage"></span>
  </div>
</div>

{{-- FECHA DE LA TRANSACCIÓN --}}
<div x-show.transition.duration.500ms="moment.value === 'other'">
  {{-- SELECCIÓN DE LA FECHA --}}
  <div class="form-group">
    <label x-bind:for="transactionDate.id" x-text="transactionDate.label" x-bind:class="{'required': transactionDate.required}"></label>
    <div class="input-group mb-2">
      <div class="input-group-prepend">
        <span class="input-group-text">
          <i class="far fa-calendar-alt"></i>
        </span>
      </div>
      <input 
        type="date" 
        x-bind:id="transactionDate.id"
        x-bind:name="transactionDate.name" 
        class="form-control"
        x-bind:class="{'is-invalid': transactionDate.hasError}"
        x-model="transactionDate.value"
        x-bind:min="transactionDate.min"
        x-bind:max="transactionDate.max"
        x-bind:required="transactionDate.required && moment.value === 'other'"
        x-bind:disabled="waiting"
        x-on:change="validateDate"
      >
  
      <div x-show="transactionDate.hasError" class="invalid-feedback" role="alert">
        <span x-text="transactionDate.errorMessage"></span>
      </div>
    </div>
  
    <div class="form-check">
      <input type="checkbox" name="setTime" id="setTime" class="form-check-input" x-model="setTime" x-bind:disabled="waiting">
      <label for="setTime" class="form-check-label">Establecer hora</label>
    </div>
  </div>

  {{-- HORA DE LA TRANSACCIÓN --}}
  <div class="form-group row" x-show.transition.duration.500ms="setTime && moment.value === 'other'">
    <label class="col-3 col-form-label" x-bind:for="transactionTime.id" x-text="transactionTime.label" x-bind:class="{'required': transactionTime.required}"></label>
    <div class="col-9">
      <input 
        x-bind:id="transactionTime.id" 
        x-bind:name="transactionTime.name" 
        type="time" 
        class="form-control" 
        x-bind:class="{'is-invalid': transactionTime.hasError}"
        x-model="transactionTime.value"
        x-bind:required="transactionTime.required && moment.value ==='other' && setTime"
        x-bind:disabled="waiting"
        x-on:change="validateTime"
      >
      <div x-show="transactionTime.hasError" class="invalid-feedback" role="alert">
        <span x-text="transactionTime.errorMessage"></span>
      </div>
    </div>
  </div>
</div>

{{-- DESCRIPCIÓN --}}
<div class="form-group">
  <label class="d-block text-center" x-bind:for="description.id" x-text="description.label" x-bind:class="{'required': description.required}"></label>
  <textarea 
    x-bind:id="description.id" 
    x-bidn:name="description.name" 
    cols="30" 
    class="form-control"
    x-bind:class="{'is-invalid': description.hasError}"
    x-bind:placeholder="description.placeholder"
    x-model="description.value"
    x-on:focus="$event.target.select()"
    x-bind:required="description.required"
    x-bind:disabled="waiting"
    x-on:change="validateDescription"
  ></textarea>

  <div x-show="description.hasError" class="invalid-feedback" role="alert">
    <span x-text="description.errorMessage"></span>
  </div>
</div>

{{-- IMPORTE DE LA TRANSACCIÓN --}}
<div class="form-group">
  <label class="d-block text-center" x-bind:for="transactionAmount.id" class="required" x-html="transactionAmount.label"></label>
  <input 
    type="text" 
    x-bind:name="transactionAmount.id" 
    x-bind:id="transactionAmount.id" 
    class="form-control text-right text-bold" 
    x-bind:class="{'is-invalid': transactionAmount.hasError}" 
    x-bind:placeholder="transactionAmount.placeholder" 
    x-ref="transactionAmount"
    x-on:input="formatAmount($event.target, 'transactionAmount')"
    x-on:focus="$event.target.select()"
    style="font-size: 1.5em;letter-spacing: 2px;"
    autocomplete="off"
    x-bind:required="transactionAmount.required"
    x-bind:disabled="waiting"
  >
  <div class="invalid-feedback" role="alert" x-show="transactionAmount.hasError" x-text="transactionAmount.errorMessage"></div>
</div>

<div class="form-group row justify-content-center" x-show.transition.500ms="transactionType.value === 'general'">
  <div class="col-6">
    <div class="form-check text-center">
      <input id="incomeRadio" class="form-check-input" type="radio" name="amountType" value="income" x-model="amountType">
      <label class="form-check-label" for="incomeRadio">Ingreso</label>
    </div>
  </div>
  <div class="col-6">
    <div class="form-check text-center">
      <input id="expenseRadio" class="form-check-input" type="radio" name="amountType" value="expense" x-model="amountType">
      <label class="form-check-label" for="expenseRadio">Egreso</label>
    </div>
  </div>
</div>