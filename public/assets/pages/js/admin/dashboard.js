window.monthlyReportsChart = undefined;
window.customersDebtsChart = undefined;
//-----------------------------------------------------------------------------
//  SE ESTABLECE EL MODELO PARA LOS DATOS DE VENTA, CREDITOS Y PAGO
//-----------------------------------------------------------------------------
window.monthlyReportsModel = () => {
  return {
    tab: 'graph',
    basicPeriod: 'annual',
    specificPeriod: 1,
    setPeriod() {
      let data = monthlyDatasets(this.basicPeriod, this.specificPeriod);
      console.log(data);
      let barChartData = {
        labels: data.labels,
        datasets: [
          {
            label: 'Ventas',
            backgroundColor: 'rgba(75, 192, 63, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            data: data.sales
          },
          {
            label: 'Abonos',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            data: data.payments
          },
          {
            label: 'Creditos',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
            data: data.credits
          },
        ]
      }
      monthlyReportsChart.data = barChartData;
      monthlyReportsChart.update();
    }
  }
}

const monthlyDatasets = (basicPeriod, specificPeriod) => {
  let labels = [];
  let sales = [];
  let credits = [];
  let payments = [];
  let reports = monthlyReports.reports;

  switch (basicPeriod) {
    case 'annual': {
      reports.forEach(report => {
        labels.push(report.month);
        sales.push(parseFloat(report.sales));
        credits.push(parseFloat(report.credits));
        payments.push(parseFloat(report.payments));
      });
    } break;
    //end case annual
    case 'biannual': {
      switch (specificPeriod) {
        case 1: {
          for (let index = 0; index < reports.length && index < 6; index++) {
            const report = reports[index];
            labels.push(report.month);
            sales.push(parseFloat(report.sales));
            credits.push(parseFloat(report.credits));
            payments.push(parseFloat(report.payments));
          }
        } break;
        case 2: {
          for (let index = 6; index < reports.length; index++) {
            const report = reports[index];
            labels.push(report.month);
            sales.push(parseFloat(report.sales));
            credits.push(parseFloat(report.credits));
            payments.push(parseFloat(report.payments));
          }
        } break;
      }
    } break;
    //end of case biannual
    case 'quarterly': {
      let from = undefined;
      let to = undefined;
      switch (specificPeriod) {
        case 1: {
          from = 0;
        } break;
        case 2:
          from = 3;
          break;
        case 3:
          from = 6;
          break;
        case 4:
          from = 9;
          break;
        default:
          from = 12
          break;
      }
      to = from + 3;

      for (let index = from; index < reports.length && index < to; index++) {
        const report = reports[index];
        labels.push(report.month);
        sales.push(parseFloat(report.sales));
        credits.push(parseFloat(report.credits));
        payments.push(parseFloat(report.payments));
      }
    } break;
  }


  return {
    labels,
    sales,
    credits,
    payments
  }
}

const customersDebtsDatasets = () =>{
  let labels = ['2019'];
  let data = [customersDebts.inititalBalance];
  let reports = customersDebts.reports;

  reports.forEach(report => {
    labels.push(report.month);
    data.push(report.balance);
  })

  return {
    labels,
    data
  }
}

/**
 * Se encarga de contruir la estructura de la grafica que
 * organiza los datos de las ventas, los creditos y los abonos
 */
const monthlyReportsBuild = () => {
  let ctx = document.getElementById('monthlyReports');
  let data = monthlyDatasets('annual', undefined);
  let barChartData = {
    labels: data.labels,
    datasets: [
      {
        label: 'Ventas',
        backgroundColor: 'rgba(75, 192, 63, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        data: data.sales
      },
      {
        label: 'Abonos',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1,
        data: data.payments
      },
      {
        label: 'Creditos',
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1,
        data: data.credits
      },
    ]
  }

  window.monthlyReportsChart = new Chart(ctx, {
    type: 'bar',
    data: barChartData,
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

const customersDebtsBuild = () => {
  let ctx = document.getElementById('customersDebts');
  let data = customersDebtsDatasets();

  console.log(data);
  let options = {
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
    maintainAspectRatio: false,
    spanGaps:false,
    elements: {
      line:{
        tension:0.000001
      }, //end line
    },//end elements
    plugins: {
      filler: {
        propagate: false
      }
    },//end plugins
    scales:{
      xAxes:[{
        ticks: {
          autoSkip: false,
          maxRotation: 0
        }
      }],//end xAxes
      yAxes: [{
        ticks: {
          beginAtZero: false,
          callback: function (value, index, values) {
            return formatCurrency(value, 0);
          }//end callback
        }//end ticks
      }], //end yAxes
    },//end scales
  }//end options

  lineChartData = {
    labels: data.labels,
    datasets: [
      {
        label: 'Deuda',
        backgroundColor: 'rgba(75, 192, 63, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        data: data.data,
        fill:'start'
      }
    ]
  }

  window.customersDebtsChart = new Chart(ctx, {
    type: 'line',
    data: lineChartData,
    options: options,
  });
}
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




document.addEventListener('livewire:load', () => {
  monthlyReportsBuild();
  customersDebtsBuild();
})