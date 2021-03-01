<div class="container-fluid">
  @if ($boxId)
      
  @else
    <div class="row justify-content-around">
      @include('livewire.cash-control.show-box.dashboard')
    </div>
  @endif
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

</script>
@endpush