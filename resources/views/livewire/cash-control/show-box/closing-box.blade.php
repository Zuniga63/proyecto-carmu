<div class="row border-top" x-show.transition.duration.700ms="closingBox">
  <h2 class="col-12 text-center my-3">Arqueo de caja</h2>
  {{-- BILLETES --}}
  <div class="col-lg-6">
    <div class="card card-primary">
      {{-- header --}}
      <div class="card-header p-1">
        <h5 class="text-center m-1">Billetes</h5>
      </div><!--/.end header -->
      {{-- body --}}
      <div class="card-body pt-0 px-2 pb-2">
        <div class="row justify-content-between mb-1 border-bottom">
          <p class="m-0 bg-light col-5">Denominación</p>
          <p class="m-0 col-2">Cant.</p>
          <p class="m-0 col-4 text-center">Importe</p>
        </div>
        {{-- 1000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$1.000</p>
          <input 
            type="number" 
            name="thousand" 
            id="thousand" 
            class="col-2" 
            x-model="banknotes.thousand.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(banknotes.thousand.amount, 0)"
          ></p>
        </div>
        {{-- 2000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$2.000</p>
          <input 
            type="number" 
            name="twoThousand" 
            id="twoThousand" 
            class="col-2" 
            x-model="banknotes.twoThousand.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(banknotes.twoThousand.amount, 0)"
          ></p>
        </div>     
        {{-- 5000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$5.000</p>
          <input 
            type="number" 
            name="fiveThousand" 
            id="fiveThousand" 
            class="col-2" 
            x-model="banknotes.fiveThousand.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(banknotes.fiveThousand.amount, 0)"
          ></p>
        </div>  
        {{-- 10.000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$10.000</p>
          <input 
            type="number" 
            name="TenThousand" 
            id="TenThousand" 
            class="col-2" 
            x-model="banknotes.tenThousand.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(banknotes.tenThousand.amount, 0)"
          ></p>
        </div>  
        {{-- 20.000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$20.000</p>
          <input 
            type="number" 
            name="twentyThousand" 
            id="twentyThousand" 
            class="col-2" 
            x-model="banknotes.twentyThousand.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(banknotes.twentyThousand.amount, 0)"
          ></p>
        </div>  
        {{-- 50.000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$50.000</p>
          <input 
            type="number" 
            name="fiftyThousand" 
            id="fiftyThousand" 
            class="col-2" 
            x-model="banknotes.fiftyThousand.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(banknotes.fiftyThousand.amount, 0)"
          ></p>
        </div>            
      </div><!--/.end body -->
      <div class="card-footer text-right">
        Total: <span class="text-bold" x-text="formatCurrency(banknotesAmount, 0)"></span>
      </div>
    </div>
  </div>

  {{-- MONEDAS --}}
  <div class="col-lg-6">
    <div class="card card-info">
      {{-- header --}}
      <div class="card-header p-1">
        <h5 class="text-center m-1">Monedas</h5>
      </div><!--/.end header -->
      {{-- body --}}
      <div class="card-body pt-0 px-2 pb-2">
        <div class="row justify-content-between mb-1 border-bottom">
          <p class="m-0 bg-light col-5">Denominación</p>
          <p class="m-0 col-2">Cant.</p>
          <p class="m-0 col-4 text-center">Importe</p>
        </div>
        {{-- 1000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$100</p>
          <input 
            type="number" 
            name="hundred" 
            id="hundred" 
            class="col-2" 
            x-model="bankCoins.hundred.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(bankCoins.hundred.amount, 0)"
          ></p>
        </div>
        {{-- 2000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$200</p>
          <input 
            type="number" 
            name="twoHundred" 
            id="twoHundred" 
            class="col-2" 
            x-model="bankCoins.twoHundred.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(bankCoins.twoHundred.amount, 0)"
          ></p>
        </div>     
        {{-- 5000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$500</p>
          <input 
            type="number" 
            name="fiveHundred" 
            id="fiveHundred" 
            class="col-2" 
            x-model="bankCoins.fiveHundred.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(bankCoins.fiveHundred.amount, 0)"
          ></p>
        </div>  
        {{-- 10.000 --}}
        <div class="row justify-content-between border-bottom py-1">
          <p class="m-0 col-5">$1000</p>
          <input 
            type="number" 
            name="Thousand" 
            id="Thousand" 
            class="col-2" 
            x-model="bankCoins.Thousand.count"
            x-on:change="updateAmounts"
            x-on:focus="$event.target.select()"
            min="0"
          >
          <p 
            class="m-0 col-4 text-right border-left" 
            x-text="formatCurrency(bankCoins.Thousand.amount, 0)"
          ></p>
        </div>          
      </div><!--/.end body -->
      {{-- footer --}}
      <div class="card-footer text-right">
        Total: <span class="text-bold" x-text="formatCurrency(bankCoinsAmount, 0)"></span>
      </div><!--/.footer -->
    </div><!--/.end card -->
  </div><!--/.end col -->

</div>