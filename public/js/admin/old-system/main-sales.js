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
  canvas.id = "salesChart";
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

window.invoiceData = () => {
  return {
    customerName: '',
    itemName: '',
    categoryId: ' ',
    quantity: 0,
    unitValue: 0,
    unitDiscount: 0,
    items: [],
    subtotal: 0,
    discount: 0,
    totalAmount: 0,
    totalItems: 0,
    showInvoice:false,
    validate() {
      return this.itemName.trim() !== ''
        && this.categoryId.trim() !== ''
        && this.quantity > 0
        && this.unitValue > 0
    },
    add() {
      if (this.validate()) {
        let itemName = this.itemName;
        let categoryId = this.categoryId;
        let quantity = this.quantity;
        let unitValue = this.unitValue;
        let unitDiscount = this.unitDiscount;
        let subTotal = quantity * unitValue;
        let discount = unitDiscount * quantity;
        let amount = subTotal - discount;

        this.subtotal += subTotal;
        this.discount += discount;
        this.totalAmount += amount;
        this.totalItems += quantity;

        this.items.push({
          itemName,
          categoryId,
          quantity,
          unitValue,
          unitDiscount,
          subTotal,
          discount,
          amount
        })

        this.resetField();
        functions.notifications('', 'Item registrado', 'success');
      } else {
        functions.notifications('', 'Faltan datos', 'warning');
      }
    },
    resetField() {
      this.itemName = '';
      this.categoryId = ' ';
      this.quantity = 0;
      this.unitValue = 0;
      this.unitDiscount = 0;
      document.getElementById('discount').value = '';
      document.getElementById('vlrUnt').value = '';
    },
    resetInvoice(){
      this.customerName = '';
      this.items = [];
      this.resetField();
    },
    printInvoice() {
      this.showInvoice = true;
      let invoiceCard = document.getElementById('invoice');
      console.log(invoiceCard);
      let ventImp = window.open(' ', 'popimpr');
      ventImp.document.write('<html><head><title>' + document.title + '</title>');
      ventImp.document.write('<link rel="stylesheet" href="http://tiendacarmu.test/assets/lte/dist/css/adminlte.min.css">');
      ventImp.document.write('<script src="http://tiendacarmu.test/assets/lte/plugins/jquery/jquery.min.js"></script>');
      ventImp.document.write('<script src="http://tiendacarmu.test/assets/lte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>');
      ventImp.document.write('</head><body>');
      ventImp.document.write(invoiceCard.innerHTML);
      ventImp.document.write('</body>');
      ventImp.document.close();
      ventImp.focus();
      ventImp.onload = ()=>{
        ventImp.print();
        ventImp.close();
      }
      this.showInvoice = false;
    }
  }
}