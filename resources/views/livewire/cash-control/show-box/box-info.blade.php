{{-- RESPONSABLE DE LA CAJA --}}
<div class="row border-bottom">
  <div class="col-5">Responsable:</div>
  <div class="col-7 text-right" x-text="box.cashier"></div>
</div>      
{{-- FECHA DE CIERRE --}}
<div class="row">
  <div class="col-5">Arqueo:</div>
  <div class="col-7">
    <p class="m-0 text-right"><span class="text-bold" x-text="box.closingDateFormatted"></span> (<span x-text="box.closingDateRelative"></span>)</p>
  </div>
</div> 
{{-- BASE --}}
<div class="row">
  <div class="col-3">Base:</div>
  <div class="col-9 text-right text-bold" x-text="formatCurrency(box.base || 0, 0)"></div>
</div> 
{{-- INGRESO Y EGRESOS --}}
<template x-if="box.transactionsByType">
  <div class="row justify-content-center">
    {{-- INGRESOS --}}
    <div class="col-12 col-lg-6"">
      <h5 class="text-center text bold mb-0 border-top border-bottom">Ingresos</h5>  
      {{-- VENTAS --}}
      <div class="row">
        <div class="col-6">Ventas:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.sale.income, 0)"></div>
      </div>  
      {{-- SERVICIOS --}}
      <div class="row" >
        <div class="col-6">Servicios:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.service.income, 0)"></div>
      </div>  
      {{-- ABONOS --}}
      <div class="row" >
        <div class="col-6">Abonos:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.payment.income, 0)"></div>
      </div> 
      {{-- Depositos --}}
      <div class="row" >
        <div class="col-6">Depositos:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.transfer.income, 0)"></div>
      </div> 
      {{-- OTROS --}}
      <div class="row" >
        <div class="col-6">Otros:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.general.income, 0)"></div>
      </div> 
      {{-- SUBTOTAL --}}
      <div class="row border-bottom border-top text-bold text-success">
        <div class="col-6">Subtotal:</div>
        <div class="col-6 text-right"  x-text="formatCurrency(box.totalIncomes || 0, 0)"></div>
      </div>   
    </div>
  
    {{-- EGRESOS --}}
    <div class="col-12 col-lg-6"">
      <h5 class="text-center text bold mb-0 border-top border-bottom">Egresos</h5>  
      {{-- VENTAS --}}
      <div class="row">
        <div class="col-6">Compras:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.purchase.expense, 0)"></div>
      </div>  
      {{-- SERVICIOS --}}
      <div class="row" >
        <div class="col-6">Gastos:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.expense.expense, 0)"></div>
      </div>  
      {{-- ABONOS --}}
      <div class="row" >
        <div class="col-6">Creditos:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.credit.expense, 0)"></div>
      </div> 
      {{-- Depositos --}}
      <div class="row" >
        <div class="col-6">Depositos:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.transfer.expense, 0)"></div>
      </div> 
      {{-- OTROS --}}
      <div class="row" >
        <div class="col-6">Otros:</div>
        <div class="col-6 text-right" x-text="formatCurrency(box.transactionsByType.general.expense, 0)"></div>
      </div> 
      {{-- SUBTOTAL --}}
      <div class="row border-bottom border-top text-bold text-success">
        <div class="col-6">Subtotal:</div>
        <div class="col-6 text-right"  x-text="formatCurrency(box.totalExpenses || 0, 0)"></div>
      </div>   
    </div>
  </div>
</template>
{{-- SALDO DE LA CAJA --}}
<div class="row">
  <p class=" col-12 text-bold text-center mb-0 mt-2" style="font-size: 2em;opacity: 0.8;" x-text="formatCurrency(box.balance, 0)"></p>
</div>