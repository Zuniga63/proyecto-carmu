{{-- base --}}
<div class="row">
  <p class="col-6 mb-0">Base</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency({{$box['base']}}, 0)"></p>
</div>   
{{-- INGRESOS --}}
<div class="row">
  <p class="col-6 mb-0">Ingresos (+):</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency({{$box['incomesAmount']}}, 0)"></p>
</div>   
{{-- Egresos --}}
<div class="row">
  <p class="col-6 mb-0">Egresos (-):</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency({{abs($box['expensesAmount'])}}, 0)"></p>
</div>  
{{-- SALDO --}}
<div class="row border-top border-bottom py-1">
  <p class="col-6 mb-0 text-bold">Saldo:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(boxBalance, 0)"></p>
</div> 
{{-- Arqueo de caja --}}
<div class="row">
  <p class="col-6 mb-0">Arqueo:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(registeredCash, 0)"></p>
</div>
{{-- Sobrante --}}
<div class="row" x-show.transition.duration.500ms="leftoverCash > 0">
  <p class="col-6 mb-0">Sobrante:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(leftoverCash, 0)"></p>
</div>
{{-- Faltante --}}
<div class="row" x-show.transition.duration.500ms="missingCash > 0">
  <p class="col-6 mb-0">Faltante:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(missingCash, 0)"></p>
</div>

{{-- IMPORTE DE LA TRANSACCIÓN --}}
<div class="form-group border-top border-bottom">
  <label for="newBase" class="required d-block text-center">Nueva Base</label>
  <input 
    type="text" 
    name="newBase" 
    id="newBase" 
    class="form-control text-right text-bold {{$errors->has('newBase') ? 'is-invalid' : ''}}" 
    placeholder="$ 0.00" 
    x-on:focus="$event.target.select()"
    x-on:input="formatInput($event.target)" 
    x-on:change="newBaseChange($event.target.value)"
    style="font-size: 1.5em;letter-spacing: 2px;"
    autocomplete="off"
  >
  @error('newBase')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- Dinero a reponer --}}
<div class="row" x-show.transition.duration.500ms="cashReplenishment > 0">
  <p class="col-6 mb-0">Depositar:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(cashReplenishment, 0)"></p>
</div>
{{-- Faltante --}}
<div class="row" x-show.transition.duration.500ms="cashTransfer > 0">
  <p class="col-6 mb-0">Retirar:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(cashTransfer, 0)"></p>
</div>

{{-- CONTRASEÑA --}}
<div class="form-group border-top border-bottom">
  <label class="required d-block text-center">Contraseña</label>
  <input 
    type="password" 
    id="password"
    class="form-control text-right text-bold" 
    x-on:change="$wire.setPassword($event.target.value)"
    x-on:focus="$event.target.select()"
    autocomplete="new-password"
    wire:ignore
  >
</div>


