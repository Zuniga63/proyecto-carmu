<div class="container-fluid">
  @if ($box)
    <div class="row justify-content-center"  x-data="model()">
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
      tab:                @entangle('tab').defer,
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
      missingCash:        @entangle('missingCash'),
      leftoverCash:       @entangle('leftoverCash'),
      cashReplenishment:  @entangle('cashReplenishment'),
      banknotes: {
        thousand: {
          count: 0,
          value: 1000
        },
        twoThousand:{
          count:0,
          value: 2000,
        },
        fiveThousand:{
          count:0,
          value: 5000,
        },
        tenThousand:{
          count:0,
          value: 10000,
        },
        twentyThousand:{
          count:0,
          value: 20000,
        },
        fiftyThousand:{
          count:0,
          value: 50000,
        },
        hundredThousand:{
          count:0,
          value: 100000,
        },
      },
      bankCoins:{
        hundred: {
          count: 0,
          value: 100
        },
        twoHundred:{
          count:0,
          value: 200,
        },
        fiveHundred:{
          count:0,
          value: 500,
        },
        Thousand:{
          count:0,
          value: 1000,
        },
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