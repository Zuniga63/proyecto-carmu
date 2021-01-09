//-----------------------------------------------------------------------------
//  UTILIDADES
//-----------------------------------------------------------------------------
window.formatCurrency = (number, fractionDigits) => {
  var formatted = new Intl.NumberFormat('es-CO', {
    style: "currency",
    currency: 'COP',
    minimumFractionDigits: fractionDigits,
  }).format(number);
  return formatted;
}

window.deleteCurrencyFormat = text => {
  let value = text.replace("$", "");
  value = value.split(".");
  value = value.join("");

  value = parseFloat(value);

  return isNaN(value) ? 0 : value;
}

window.formatInput = (target) => {
  console.log(target.value)
  let value = target.value;
  value = deleteCurrencyFormat(value);

  target.value = formatCurrency(value, 0);
}

document.addEventListener('livewire:load', () => {
  Livewire.on('transactionIsOk', (transactionType) => {
    let title = transactionType === 'credit' 
              ? `¡Crédito registrado!` 
              : '¡Abono registrado!';
    let body = '';
    let type = 'success';
    functions.notifications(body, title, type);
    document.getElementById('transactionAmount').value = '';
  });
  Livewire.on('transactionIsDeleted', (transactionType) => {
    let title = transactionType === 'credit' 
              ? `¡Crédito eliminado!` 
              : '¡Abono eliminado!';
    let body = '';
    let type = 'success';
    functions.notifications(body, title, type);
    document.getElementById('transactionAmount').value = '';
  });

  Livewire.on('storeError', ()=>{
    let title = `¡Oops, algo salio mal!`;
    let body = 'Algo en el servidor no funcionó correctamente';
    let type = 'error';
    functions.notifications(body, title, type);
  })

  Livewire.on('customerNotFound', ()=>{
    let title = `¡Oops, algo salio mal!`;
    let body = 'El cliente no existe o ha sido eliminado';
    let type = 'error';
    functions.notifications(body, title, type);
  })

  Livewire.on('transactionTypeError', ()=>{
    let title = `¡Oops, algo salio mal!`;
    let body = 'El tipo de transacción no es valido';
    let type = 'error';
    functions.notifications(body, title, type);
  })
})