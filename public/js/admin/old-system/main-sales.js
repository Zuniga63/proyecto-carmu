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
  let value = target.value;
  value = deleteCurrencyFormat(value);

  target.value = formatCurrency(value, 0);
}

window.updateGraph = data => {
  document.getElementById('salesChart').remove();
  const canvas = document.createElement("canvas");
  canvas.id="salesChart";
  document.getElementById('graphContainer').appendChild(canvas);

  let ctx = document.getElementById('salesChart');
  let graph = new Chart(ctx, {
    type: data.type,
    data: {
      labels: data.labels,
      datasets: data.datasets,
    },
    options: {
      responsive: true,
      legend: {
        position: 'top'
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            callback: function (value, index, values) {
              return formatCurrency(value, 0);
            }//end callback
          }//end ticks
        }], //end yAxes
      },//end scales
      tooltips: {
        callbacks: {
          label: (tooltipItem, data) => {
            let dataset = data.datasets[tooltipItem.datasetIndex];
            let label = dataset.label || '';
            let currentValue = dataset.data[tooltipItem.index]

            if (label) {
              label += ': ';
            }

            label += formatCurrency(currentValue, 0);
            return label;
          }, //end label
        },//end callbacks
      },//end tooltips
    }
  });
}

document.addEventListener('livewire:load', () => {
  Livewire.on('stored', (transactionType) => {
    let title = "Venta registrada!"
    let body = '';
    let type = 'success';
    functions.notifications(body, title, type);
  });

  Livewire.on('reset', () => {
    document.getElementById('saleAmount').value = '';
  })

  Livewire.on('saleMount', (amount) => {
    let title = "¡Los datos han sido cargados!"
    let body = '';
    let type = 'success';
    functions.notifications(body, title, type);

    document.getElementById('saleAmount').value = formatCurrency(amount, 0);
  })

  Livewire.on('updated', () => {
    let title = "¡Datos actualizados!"
    let body = '';
    let type = 'success';
    functions.notifications(body, title, type);
  })

  Livewire.on('serverError', () => {
    let title = `¡Oops, algo salio mal!`;
    let body = 'Algo en el servidor no funcionó correctamente';
    let type = 'error';
    functions.notifications(body, title, type);
  })

  // Livewire.on('customerNotFound', ()=>{
  //   let title = `¡Oops, algo salio mal!`;
  //   let body = 'El cliente no existe o ha sido eliminado';
  //   let type = 'error';
  //   functions.notifications(body, title, type);
  // })

  // Livewire.on('transactionTypeError', ()=>{
  //   let title = `¡Oops, algo salio mal!`;
  //   let body = 'El tipo de transacción no es valido';
  //   let type = 'error';
  //   functions.notifications(body, title, type);
  // })
})