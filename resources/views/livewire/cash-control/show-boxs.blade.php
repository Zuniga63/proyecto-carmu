<div class="container-fluid">
  @if ($box)
    <div class="row justify-content-center"  x-data="model()" x-init="updateAmounts">
      <div class="col-lg-4 mb-4" x-show.transition.duration.500ms="tab === 'transactions' || closingBox">
        @include('livewire.cash-control.show-box.form')
      </div>
      <div class="col-lg-8">
        @include('livewire.cash-control.show-box.box-panel')
      </div>
    </div>
  @else
    <div class="row justify-content-around">
      @include('livewire.cash-control.show-box.dashboard')
    </div>
  @endif
</div>

@push('scripts')
<script>
  window.model = () => {
    return {
      tab:                @entangle('tab'),
      state:              @entangle('state'),
      closingBox:         @entangle('closingBox'),
      transactionType:    @entangle('transactionType'),
      moment:             @entangle('moment'),
      transactionDate:    @entangle('transactionDate'),
      setTime:            @entangle('setTime'),
      transactionTime:    @entangle('transactionTime'),
      description:        @entangle('description'),
      transactionAmount:  @entangle('transactionAmount'),
      amountType:         @entangle('amountType'),
      newBase:            @entangle('newBase'),
      destinationBox:     @entangle('destinationBox'),
      registeredCash:     @entangle('registeredCash'),
      boxBalance:         @entangle('boxBalance'),
      missingCash:        @entangle('missingCash'),
      leftoverCash:       @entangle('leftoverCash'),
      cashReplenishment:  @entangle('cashReplenishment'),
      cashTransfer:       0,
      banknotes: {
        thousand: {
          count: 0,
          value: 1000,
          amount: 0,
        },
        twoThousand:{
          count:0,
          value: 2000,
          amount: 0,
        },
        fiveThousand:{
          count:0,
          value: 5000,
          amount: 0,
        },
        tenThousand:{
          count:0,
          value: 10000,
          amount: 0,
        },
        twentyThousand:{
          count:0,
          value: 20000,
          amount: 0,
        },
        fiftyThousand:{
          count:0,
          value: 50000,
          amount: 0,
        },
        hundredThousand:{
          count:0,
          value: 100000,
          amount: 0,
        },
      },
      bankCoins:{
        hundred: {
          count: 0,
          value: 100,
          amount: 0
        },
        twoHundred:{
          count:0,
          value: 200,
          amount: 0
        },
        fiveHundred:{
          count:0,
          value: 500,
          amount: 0
        },
        Thousand:{
          count:0,
          value: 1000,
          amount: 0
        },
      },
      banknotesAmount: 0,
      bankCoinsAmount: 0,
      updateAmounts(){
        let banknotesAmount = 0;
        let bankCoinsAmount = 0;

        for(const [key, bill] of Object.entries(this.banknotes)){
          bill.amount = parseInt(bill.count) * bill.value;
          banknotesAmount += bill.amount;
        }

        for(const [key, coin] of Object.entries(this.bankCoins)){
          coin.amount = parseInt(coin.count) * coin.value;
          bankCoinsAmount += coin.amount;
        }

        this.banknotesAmount = banknotesAmount;
        this.bankCoinsAmount = bankCoinsAmount;
        this.registeredCash = bankCoinsAmount + banknotesAmount;
        let boxBalance = parseInt(this.boxBalance);
        if(boxBalance != this.registeredCash){
          if(this.boxBalance > this.registeredCash){
            this.missingCash = this.boxBalance - this.registeredCash;
            this.leftoverCash = 0;
          }else{
            this.leftoverCash = this.registeredCash - this.boxBalance;
            this.missingCash = 0;
          }
        }else{
          this.missingCash = 0;
          this.leftoverCash = 0;
        }

        //Se actualiza el estado del cierre
        if(this.newBase >= this.registeredCash){
          this.cashReplenishment = this.newBase - this.registeredCash;
          this.cashTransfer = 0;
        }else{
          this.cashTransfer = this.registeredCash - this.newBase;
          this.cashReplenishment = 0;
        }
      },
      newBaseChange(value) {
        this.newBase = deleteCurrencyFormat(value);
        this.updateAmounts();
      }
    }
  }

  window.formatCurrency = (number, fractionDigits) => {
    var formatted = new Intl.NumberFormat('es-CO', {
      style: "currency",
      currency: 'COP',
      minimumFractionDigits: fractionDigits,
    }).format(number);
    return formatted;
  }

  /**
   * Este metodo se encarga de eliminar el formateado 
   * que le proporciona el metodo formatcurrency
   * y retorna un numero float
   */
  window.deleteCurrencyFormat = text => {
    let value = text.replace("$", "");
    value = value.split(".");
    value = value.join("");

    value = parseFloat(value);

    return isNaN(value) ? 0 : value;
  }

  window.formatInput = (target) => {
    let value = target.value;
    value = deleteCurrencyFormat(value);

    target.value = formatCurrency(value, 0);
  }

  window.addEventListener('livewire:load', ()=>{

    Livewire.on('alert', (title, message, type) => {
      functions.notifications(message, title, type);
      console.log(message);
    })

    Livewire.on('reset', () => {
      document.getElementById('transactionAmount').value = '';
      document.getElementById('password').value = '';
      document.getElementById('newBase').value = '';
    })

    Livewire.on('updateAmount', (amount, newBase) => {
      document.getElementById('transactionAmount').value = formatCurrency(amount, 0);
      document.getElementById('newBase').value = formatCurrency(newBase, 0);
    })

    Livewire.on('viewRender', (data)=>{
      let countLabel = `Meses: ${data.months}`;
      document.getElementById('labelCount').setAttribute('data-original-title', countLabel);
      document.getElementById('capital').value = formatCurrency(data.capital, 0);
      document.getElementById('interests').value = formatCurrency(data.interests, 0);
      document.getElementById('annuity').value = formatCurrency(data.annuity, 0);
      document.getElementById('rest').value = formatCurrency(data.rest, 0);
    })
  });
</script>
@endpush