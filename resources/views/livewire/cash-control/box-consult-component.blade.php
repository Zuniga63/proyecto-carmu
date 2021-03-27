<div>
    @include('livewire.cash-control.box-consult.table')
</div>

@push('scripts')
<script>
  
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