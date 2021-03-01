@foreach ($this->boxs as $box)
<div class="col-lg-6" x-data>
  <div class="card {{$box['business'] == 'Tienda Carmú' ? 'card-success' : ($box['business'] == 'Son de Cuatro' ? 'card-primary' : 'card-secondary')}}">
    <div class="card-header text-center">
      <h5 class="text-bold mb-0">{{$box['name']}}</h5>
      <p class="m-0" style="font-size: 0.8em">({{$box['business']}})</p>
    </div>
    <div class="card-body">
      {{-- RESPONSABLE DE LA CAJA --}}
      <div class="row border-bottom">
        <div class="col-5">Responsable:</div>
        <div class="col-7 text-right">{{$box['cashier']}}</div>
      </div>      
      {{-- FECHA DE CIERRE --}}
      <div class="row">
        <div class="col-3">Cierre Anterior:</div>
        <div class="col-9 text-right">{{$box['closeDate']}}</div>
      </div> 
      {{-- BASE --}}
      <div class="row">
        <div class="col-3">Base:</div>
        <div class="col-9 text-right text-bold" x-text="formatCurrency({{$box['base']}}, 0)"></div>
      </div> 
      {{-- INGRESO Y EGRESOS --}}
      <div class="row">
        {{-- INGRESOS --}}
        <div class="col-12 col-lg-6">
          <h5 class="text-center text bold mb-0 border-top border-bottom">Ingresos</h5>  
          {{-- VENTAS --}}
          <div class="row">
            <div class="col-6">Ventas:</div>
            <div class="col-6 text-right" x-text="formatCurrency({{$box['sales']}}, 0)"></div>
          </div>  
          {{-- SERVICIOS --}}
          <div class="row">
            <div class="col-6">Servicios:</div>
            <div class="col-6 text-right" x-text="formatCurrency({{$box['services']}}, 0)"></div>
          </div>      
          {{-- ABONOS --}}
          <div class="row">
            <div class="col-6">Abonos:</div>
            <div class="col-6 text-right" x-text="formatCurrency({{$box['payments']}}, 0)"></div>
          </div> 
          {{-- OTROS --}}
          <div class="row">
            <div class="col-6">Otros:</div>
            <div class="col-6 text-right" x-text="formatCurrency({{$box['otherIncomes']}}, 0)"></div>
          </div>   
          {{-- SUBTOTAL --}}
          <div class="row border-bottom border-top text-bold text-success">
            <div class="col-6">Subtotal:</div>
            <div class="col-6 text-right"  x-text="formatCurrency({{$box['incomesAmount']}}, 0)"></div>
          </div>   
        </div>
  
        {{-- EGRESOS --}}
        <div class="col-12 col-lg-6">
          <h5 class="text-center text bold mb-0 border-top border-bottom">Egresos</h5>  
          {{-- GASTOS --}}
          <div class="row">
            <div class="col-6">Gastos:</div>
            <div class="col-6 text-right" x-text="formatCurrency({{$box['expenses']}}, 0)"></div>
          </div>    
          {{-- COMPRAS --}}
          <div class="row">
            <div class="col-6">Compras:</div>
            <div class="col-6 text-right"  x-text="formatCurrency({{$box['purchases']}}, 0)"></div>
          </div>   
          {{-- CREDITOS --}}
          <div class="row">
            <div class="col-6">Creditos:</div>
            <div class="col-6 text-right"  x-text="formatCurrency({{$box['credits']}}, 0)"></div>
          </div>     
          {{-- OTROS --}}
          <div class="row">
            <div class="col-6">Otros:</div>
            <div class="col-6 text-right" x-text="formatCurrency({{$box['otherExpenses']}}, 0)"></div>
          </div> 
          {{-- SUBTOTAL --}}
          <div class="row border-bottom border-top text-bold text-danger">
            <div class="col-6">Subtotal:</div>
            <div class="col-6 text-right" x-text="formatCurrency({{$box['expensesAmount']}}, 0)"></div>
          </div>   
        </div>    
      </div>
      {{-- SALDO DE LA CAJA --}}
      <div class="row">
        <p class=" col-12 text-bold text-center mb-1" style="font-size: 2em;opacity: 0.8;" x-text="formatCurrency({{$box['balance']}}, 0)"></p>
      </div>
    </div><!--/.end body -->

    <div class="card-footer row">
      <button class="btn {{$box['business'] == 'Tienda Carmú' ? 'btn-success' : ($box['business'] == 'Son de Cuatro' ? 'btn-primary' : 'btn-secondary')}} col-12">Hacer Cierre</button>
    </div>
  </div><!--/.end card -->
</div><!--/.end col -->
@endforeach