<div x-data="invoiceData()">
  <h2 class="text-center">Sistema de Facturación</h2>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="form-group row col-10">
        <label for="invoiceCustomerName" class="col-2">Cliente</label>
        <input 
          type="text" 
          name="customerName" 
          id="invoiceCustomerName" 
          class="col-10 form-control" 
          autocomplete="off" 
          placeholder="Nombre del cliente"
          x-model="customerName"
        >
      </div>
    </div>

    <div class="row">
      <div class="card card-default col-12">
        <div class="card-header">
          <h3 class="card-title">Agregar Articulos</h3>
          <div class="card-tools">
            <button class="btn btn-tool" data-card-widget="collapse" style="color: #adb5bd">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        
        <div class="card-body p-0">
          <div class="row align-items-center">
            <div class="col-11 row justify-content-between">
              <div class="form-group row col-7">
                <label for="itemName" class="col-3">Producto</label>
                <input 
                  type="text" 
                  name="itemName" 
                  id="itemName" 
                  class="form-control col-9"
                  x-model="itemName"
                >
              </div>
              <div class="form-group row col-5">
                <label for="itemCategory" class="col-4">Categoría</label>
                <select 
                  name="itemCategory" 
                  id="itemCategory" 
                  class="form-control col-8" 
                  x-model="categoryId" 
                  x-on:input="console.log($event.target)"
                >
                  <option value=" " disabled>Selecciona una</option>
                  @foreach ($this->categories as $id => $name)
                  <option value="{{$id}}">{{$name}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group row col-4">
                <label for="itemCount" class="col-5">Cant</label>
                <input 
                  type="number" 
                  min="1" 
                  name="itemCount" 
                  id="itemCount" 
                  class="form-control col-7"
                  x-model.number="quantity"
                >
              </div>
              <div class="form-group row col-4">
                <label for="vlrUnt" class="col-4">Vlr. Unt</label>
                <input 
                  type="text" 
                  name="vlrUnt" 
                  id="vlrUnt" 
                  class="form-control col-8 text-right"
                  x-on:input="formatInput($event.target)"
                  x-on:change="unitValue = deleteCurrencyFormat($event.target.value)"
                >
              </div>
              <div class="form-group row col-4">
                <label for="discount" class="col-5">Desc. Unt</label>
                <input 
                  type="text" 
                  name="discount" 
                  id="discount" 
                  class="form-control col-7"
                  x-on:input="formatInput($event.target)"
                  x-on:change="unitDiscount = deleteCurrencyFormat($event.target.value)"
                >
              </div>
            </div>
            <div class="col-1">
              <button 
                class="btn btn-success rounded-circle ml-4" 
                x-on:click="add()"
              >
                <i class="fas fa-plus"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="card card-default col-12">
        <div class="card-header">
          <h3 class="card-title">Articulos a Facturar</h3>
          <div class="card-tools">
            <button class="btn btn-tool" data-card-widget="collapse" style="color: #adb5bd">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        
        <div class="card-body table-responsive p-0" style="height: 50vh;">
          <table class="table table-head-fixed table-hover text-nowrap">
            <thead>
              <tr class="text-center">
                <th>Cant.</th>
                <th class="text-left">Nombre</th>
                <th class="text-right">Vlr. Unt</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Desc.</th>
                <th class="text-right">Importe</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(item, index) in items" :key="index">
                <tr>
                  <td class="text-center" x-text="item.quantity"></td>
                  <td class="text-left" x-text="item.itemName"></td>
                  <td class="text-right" x-text="formatCurrency(item.unitValue, 0)"></td>
                  <td class="text-right" x-text="formatCurrency(item.subTotal, 0)"></td>
                  <td class="text-right" x-text="formatCurrency(item.discount, 0)"></td>
                  <td class="text-right" x-text="formatCurrency(item.amount, 0)"></td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="row justify-content-between align-items-center">
      <div class="col-8">
        <div class="d-flex justify-content-around">
          <button class="btn btn-primary" x-on:click="printInvoice()">Imprimir Factura</button>
          <button class="btn btn-success" disabled>Registrar Venta</button>
          <button class="btn btn-danger" x-on:click="resetInvoice()">Cancelar</button>
          <div class="form-check">
            <input type="checkbox" name="showInvoice" id="showInvoice" class="form-check-input" x-model="showInvoice">
            <label for="showInvoice" class="form-check-label">Mostrar factura</label>
          </div>
        </div>
      </div>

      <div class="col-4 border border-secundary rounded px-2 pt-2 pb-0">
        <div class="row">
          <p class="text-right col-4">Subtotal:</p>
          <p class="col-7 ml-2 text-bold" x-text="formatCurrency(subtotal, 0)"></p>
        </div>
        <div class="row">
          <p class="text-right col-4">Descuento:</p>
          <p class="col-7 ml-2 text-bold" x-text="formatCurrency(discount, 0)"></p>
        </div>
        <div class="row">
          <p class="text-right col-4">Total:</p>
          <p class="col-7 ml-2 text-bold" x-text="formatCurrency(totalAmount, 0)"></p>
        </div>
      </div>
    </div>

    <div class="pt-5" x-show.transition="showInvoice">
      <div style="width: 80mm" class="mx-auto border border-secundary p-2" id="invoice">
        <h4 class="text-center text-bold text-uppercase mb-0">Tienda Carmú</h4>
        <p class="text-normal mb-0 text-center h6"><small>Regimen Simplificado</small></p>
        <p class="text-normal mb-0 text-center h6"><small>Nit: <span class="text-bold">1098617663-1</span></small></p>
        <p class="font-weight-lighter text-center mb-0">Ropa exclusiva, relojería y accesorios</p>
        <p class="text-normal mb-0 text-center text-uppercase h6">C.c Ibirico Plaza Local 15</p>
        <div class="dropdown-divider"></div>
        <p class="text-normal mb-0 text-left h6">Factura Numero: 000525</p>
        <p class="text-normal mb-0 text-left h6">Fecha: {{$this->maxDate}}</p>
        <p class="text-normal mb-0 text-left h6">Cliente: <span class="text-uppercase" x-text="customerName"></span></p>
        <table class="table text-nowrap">
          <thead>
            <tr class="text-center">
              <th style="font-size: 0.8rem">Cant.</th>
              <th class="text-left" style="font-size: 0.8rem">Nombre</th>
              <th class="text-right" style="font-size: 0.8rem">Importe</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(item, index) in items" :key="index">
              <tr>
                <td class="text-center p-0" x-text="item.quantity" style="font-size: 0.8rem"></td>
                <td class="text-left p-0" x-text="item.itemName" style="font-size: 0.8rem"></td>
                <td class="text-right p-0" x-text="formatCurrency(item.amount, 0)" style="font-size: 0.8rem"></td>
              </tr>
            </template>
          </tbody>
        </table>
  
        <div class="dropdown-divider"></div>
        <div>
          <div class="row mb-0">
            <p class="text-right col-6 mb-0">Subtotal:</p>
            <p class="col-5 ml-2 text-bold mb-0" x-text="formatCurrency(subtotal, 0)"></p>
          </div>
          <div class="row mb-0">
            <p class="text-right col-6 mb-0">Descuento:</p>
            <p class="col-5 ml-2 text-bold mb-0" x-text="formatCurrency(discount, 0)"></p>
          </div>
          <div class="row mb-0">
            <p class="text-right col-6 mb-0">Total Factura:</p>
            <p class="col-5 ml-2 text-bold mb-0" x-text="formatCurrency(totalAmount, 0)"></p>
          </div>
        </div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-divider"></div>
        <div class="row justify-content-between">
          <p class="mb-0 col-3" style="font-size: 0.8rem">Caja:01</p>
          <p class="mb-0 col-4" style="font-size: 0.8rem">Productos: <span x-text="items.length"></span></p>
          <p class="mb-0 col-3" style="font-size: 0.8rem">Articulos: <span x-text="totalItems"></span></p>
          <p class="mb-0 col-12">Vendedor: <span class="text-uppercase">{{auth()->user()->name}}</span></p>
        </div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-divider"></div>
        <p class="text-center mb-0"><small>Esta factura se asimila a una letra de cambio para todos los efectos legales</small></p>
        <p class="text-center mb-0"><small>(Art. 779 del codigo de comercio)</small></p>
        <p class="text-normal text-center h6">www.tiendacarmu.com</p>
      </div>
    </div>
  </div>
</div>