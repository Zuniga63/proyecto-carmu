window.monthlyReportsChart = undefined;
window.customersDebtsChart = undefined;
//-----------------------------------------------------------------------------
//  SE ESTABLECE EL MODELO PARA LOS DATOS DE VENTA, CREDITOS Y PAGO
//-----------------------------------------------------------------------------
window.monthlyReportsModel = () => {
  return {
    tab: 'sales',
    periodName: 'monthly',
    month: 0,
    tremester: 0,
    semester: 0,
    accumulated: true,
    initChart() {
      this.periodName = 'monthly';
      this.month = data.month;
      this.tremester = data.tremester;
      this.semester = data.semester;
      this.updateChart();
    },//end initChart
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
    },
    changePeriodName() {
      this.accumulated = false;
      switch (this.periodName) {
        case 'monthly':
          this.month = data.month;
          this.accumulated = true;
          this.updateChart();
          break;
        case 'quarterly':
          this.tremester = data.tremester;
          this.updateChart();
          break;
        case 'biannual':
          this.semester = data.semester;
          this.updateChart();
          break;
        default:
          this.updateChart();
          break;
      }
    },
    updateChart() {
      let type = 'line';
      switch (this.periodName) {
        case 'quarterly':
          type = this.accumulated ? 'line' : 'bar';
          break;
      }

      window.destroyCanvas('monthlyReports');
      let ctx = document.getElementById('monthlyReports');
      window.monthlyReportsChart = new Chart(ctx, {
        type,
        data: this.getDatasets(),
        options: this.getChartOptions(),
      })
    },
    getDatasets() {
      switch (this.tab) {
        case 'sales':
          return this.getSaleDatasets();
          break;
      }//end switch
    },
    getSaleDatasets() {
      let labels = [];
      let datasets = [];
      let lastYear = true;

      switch (this.periodName) {
        case 'monthly':
          let maxDays = 0;
          let monthIndex = this.month - 1;
          data.sales.forEach(annualSale => {
            let sales = [];
            let dailySales = annualSale.monthlySales[monthIndex].dailySales;
            /**
             * Se recuperan las ventas parciales o acumuladas
             * de cada día
             */
            if (this.accumulated) {
              sales = dailySales.map(sale => sale.accumulated);
            } else {
              sales = dailySales.map(sale => sale.partial);
            }

            /**
             * En este punto se define el numero maximo de días 
             * que se van a imprimir en el canvas
             */
            maxDays = maxDays >= sales.length ? maxDays : sales.length;

            let dataset = {
              label: annualSale.year,
              backgroundColor: lastYear ? window.chartColors.red : window.chartColors.green,
              borderColor: lastYear ? window.chartColors.red : window.chartColors.green,
              borderWidth: 1,
              data: sales,
              fill: false
            }

            if (lastYear) {
              dataset.borderDash = [5, 5]
            }

            datasets.push(dataset);
            lastYear = false;
          });//end forEach

          for (let i = 1; i <= maxDays; i++) {
            labels.push(i);
          }//end for
          break;//End case: monthly
        case 'quarterly':
          let tremester = this.tremester;
          data.sales.forEach(annualSale => {
            let sales = [];
            /**
             * Con este codigo repuero los meses que 
             * correspondel al trimestre
             */
            let monthlySales = annualSale.monthlySales.filter(x => {
              let realTremestre = x.month / 3.0;
              return realTremestre > tremester - 1 && realTremestre <= tremester;
            })
            /**
             * Ahora se recupera solo las ventas mensuales
             * parciales de cada mes.
             */
            if (this.accumulated) {
              let accumulateValue = 0;
              sales = monthlySales.map((x) => {
                accumulateValue += x.partial;
                return accumulateValue;
              })
            }else{
              sales = monthlySales.map(x => x.partial);
            }

            let bgColor = lastYear ? window.chartColors.red : window.chartColors.green;

            /**
             * Ahora se guarda el dataset con las ventas parciales
             * de cada mes
             */
            let dataset = {
              label: annualSale.year,
              backgroundColor: window.color(bgColor).alpha(0.5).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: sales,
            }

            if(this.accumulated){
              dataset.fill = false;
            }

            datasets.push(dataset);
            lastYear = false;
          });//end forEach

          /**
           * Ahora se definen las etiquetas generales con
           * los nombres de los meses del trimestre
           */
          data.months.forEach((monthName, index) => {
            let realTremester = (index + 1) / 3;
            let min = tremester - 1;
            if (min < realTremester && realTremester <= tremester) {
              labels.push(monthName);
            }
          })// end forEach
          break;//end case quarterly
        case 'biannual':
          let semester = this.semester;
          data.sales.forEach(annualSale => {
            let sales = [];
            /**
             * Con este codigo repuero los meses que 
             * correspondel al semester
             */
            let monthlySales = annualSale.monthlySales.filter(x => {
              let realSemester = x.month / 6.0;
              return realSemester > semester - 1 && realSemester <= semester;
            });
            /**
             * Ahora se recupera solo las ventas mensuales
             * parciales de cada mes.
             */
            if (this.accumulated) {
              let accumulateAmount = 0;
              sales = monthlySales.map(x => accumulateAmount += x.partial)
            } else {
              sales = monthlySales.map(x => x.partial)
            }

            let bgColor = lastYear ? window.chartColors.red : window.chartColors.green;

            /**
             * Ahora se guarda el dataset con las ventas parciales
             * de cada mes
             */
            let dataset = {
              label: annualSale.year,
              // type: 'bar',
              backgroundColor: window.color(bgColor).alpha(0.5).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: sales,
              fill: false
            }

            datasets.push(dataset);
            lastYear = false;
          });//end forEach

          /**
           * Ahora se definen las etiquetas generales
           */
          data.months.forEach((monthName, index) => {
            let realSemester = (index + 1) / 6.0;
            let min = semester - 1;
            let max = semester;
            if (min < realSemester && realSemester <= semester) {
              labels.push(monthName);
            }
          })// end forEach
          break;//end case biannual
        case 'annual':
          data.sales.forEach(annualSale => {
            let sales = [];
            /**
             * Con este codigo repuero los meses que 
             * correspondel al semester
             */
            let monthlySales = annualSale.monthlySales;
            /**
             * Ahora se recupera solo las ventas mensuales
             * parciales de cada mes.
             */
            if (this.accumulated) {
              sales = monthlySales.map(x => x.accumulated)
            } else {
              sales = monthlySales.map(x => x.partial)
            }
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.green;

            /**
             * Ahora se guarda el dataset con las ventas parciales
             * de cada mes
             */
            let dataset = {
              label: annualSale.year,
              // type: 'bar',
              backgroundColor: window.color(bgColor).alpha(0.5).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: sales,
              fill: false
            }

            datasets.push(dataset);
            lastYear = false;
          });//end forEach

          /**
           * Ahora se definen las etiquetas 
           * para los nombres de los meses
           */
          labels = data.months;
          break;
      }//end switch

      return { labels, datasets };
    },
    getMonthly() {

    },
    getChartOptions() {
      return {
        responsive: true,
        legend: {
          position: 'top'
        },//end legend
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

const customersDebtsDatasets = () => {
  let labels = ['2020'];
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

const salesByCategoriesDatasets = () => {
  let labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
  let datasets = [];
  let colors = [
    '255, 99, 132',
    '255, 159, 64',
    '255, 205, 86',
    '75, 192, 192',
    '54, 162, 235',
    '153, 102, 255',
    '201, 203, 207',
  ];
  let indexColor = 0;

  salesByCategories.forEach(category => {
    let color = colors[indexColor];
    indexColor++;

    datasets.push({
      label: category.name,
      backgroundColor: `rgba(${color}, 0.4)`,
      borderColor: `rgba(${color}, 1)`,
      borderWidth: 1,
      data: category.sales
    })

    indexColor = indexColor >= 7 ? 0 : indexColor;
  })

  return {
    labels,
    datasets
  }
}

const salesByCategoriesBuild = () => {
  let ctx = document.getElementById('salesByCategories');
  let data = salesByCategoriesDatasets();
  let barChartData = {
    labels: data.labels,
    datasets: data.datasets,
  }

  window.salesByCategoriesChart = new Chart(ctx, {
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
  })
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
    maintainAspectRatio: true,
    spanGaps: true,
    elements: {
      line: {
        tension: 0.000001
      }, //end line
    },//end elements
    plugins: {
      filler: {
        propagate: false
      }
    },//end plugins
    scales: {
      xAxes: [{
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
        fill: 'start'
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
/**
 * Helper de chartjs para poder trabajar comodamente
 * con los colores
 */
window.color = Chart.helpers.color;

window.formatCurrency = (number, fractionDigits) => {
  var formatted = new Intl.NumberFormat('es-CO', {
    style: "currency",
    currency: 'COP',
    minimumFractionDigits: fractionDigits,
  }).format(number);
  return formatted;
}

window.chartColors = {
  red: 'rgb(255, 99, 132)',
  orange: 'rgb(255, 159, 64)',
  yellow: 'rgb(255, 205, 86)',
  green: 'rgb(75, 192, 192)',
  blue: 'rgb(54, 162, 235)',
  purple: 'rgb(153, 102, 255)',
  grey: 'rgb(201, 203, 207)'
};

window.destroyCanvas = id => {
  document.getElementById(id).remove();

  const canvas = document.createElement("canvas");
  canvas.id = id;

  document.getElementById(id + 'CanvasContainer').appendChild(canvas);
}


document.addEventListener('livewire:load', () => {
  // monthlyReportsBuild();
  customersDebtsBuild();
  salesByCategoriesBuild();
})