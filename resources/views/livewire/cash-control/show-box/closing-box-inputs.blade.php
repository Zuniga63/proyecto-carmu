{{-- Negocio --}}
<div class="row">
  <p class="col-6 mb-0">Negocio</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="box.business"></p>
</div>   
{{-- Caja --}}
<div class="row">
  <p class="col-6 mb-0">Caja</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="box.name"></p>
</div>   
{{-- Responsable --}}
<div class="row border-bottom">
  <p class="col-6 mb-0">Cajero</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="box.cashier"></p>
</div>   
{{-- base --}}
<div class="row">
  <p class="col-6 mb-0">Base</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(box.base, 0)"></p>
</div>   
{{-- INGRESOS --}}
<div class="row">
  <p class="col-6 mb-0">Ingresos (+):</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(box.totalIncomes, 0)"></p>
</div>   
{{-- Egresos --}}
<div class="row">
  <p class="col-6 mb-0">Egresos (-):</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(box.totalExpenses, 0)"></p>
</div>  
{{-- SALDO --}}
<div class="row border-top border-bottom py-1">
  <p class="col-6 mb-0 text-bold">Saldo:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(box.balance, 0)"></p>
</div> 
{{-- Arqueo de caja --}}
<div class="row">
  <p class="col-6 mb-0">Arqueo:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(cashRegister, 0)"></p>
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
<div class="form-group">
  <label class="d-block text-center" x-bind:for="newBase.id" class="required" x-html="newBase.label"></label>
  <input 
    type="text" 
    x-bind:name="newBase.id" 
    x-bind:id="newBase.id" 
    class="form-control text-right text-bold" 
    x-bind:class="{'is-invalid': newBase.hasError}" 
    x-bind:placeholder="newBase.placeholder" 
    x-ref="newBase"
    x-on:input="formatAmount($event.target, 'newBase')"
    x-on:focus="$event.target.select()"
    style="font-size: 1.5em;letter-spacing: 2px;"
    autocomplete="off"
    x-bind:required="newBase.required"
    x-bind:disabled="waiting"
  >
  <div class="invalid-feedback" role="alert" x-show="newBase.hasError" x-text="newBase.errorMessage"></div>
</div>

{{-- Dinero a reponer --}}
<div class="row" x-show.transition.duration.500ms="cashReplenishment > 0">
  <p class="col-6 mb-0">Mover de caja mayor:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(cashReplenishment, 0)"></p>
</div>
{{-- Faltante --}}
<div class="row" x-show.transition.duration.500ms="cashTransfer > 0">
  <p class="col-6 mb-0">Transferir a caja mayor:</p>
  <p class="col-6 mb-0 text-right text-bold" x-text="formatCurrency(cashTransfer, 0)"></p>
</div>

{{-- CONTRASEÑA --}}
{{-- <div class="form-group border-top border-bottom">
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
</div> --}}


