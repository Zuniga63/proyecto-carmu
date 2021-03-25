window.monthlyReportsChart = undefined;
window.debtEvolutionChart = undefined;
//-----------------------------------------------------------------------------
//  SE ESTABLECE EL MODELO PARA LOS DATOS DE VENTA, CREDITOS Y PAGO
//-----------------------------------------------------------------------------
window.monthlyReportsModel = () => {
  return {
    tab: 'sales',
    title: '',
    periodName: 'monthly',
    month: 0,
    tremester: 0,
    semester: 0,
    accumulated: true,
    showLastYear: false,
    initChart() {
      this.tab = 'sales'
      this.periodName = 'monthly';
      this.month = data.month;
      this.tremester = data.tremester;
      this.semester = data.semester;
      this.updateChart();
    },//end initChart
    changeTab(tab) {
      if (this.tab != tab) {
        this.tab = tab;
        this.updateChart();
      }
    },
    updateTitle() {
      let period = `[${data.year - 1} - ${data.year}]`
      switch (this.tab) {
        case 'sales':
          this.title = `Ventas ${period}`
          break;
        case 'payments':
          this.title = `Abonos ${period}`
          break;
        case 'credits':
          this.title = `Creditos ${period}`
          break;
        case 'mixed':
          if (this.showLastYear) {
            this.title = `Ventas, Creditos y Abonos [${data.year - 1}]`
          } else {
            this.title = `Ventas, Creditos y Abonos [${data.year}]`
          }
          break;

      }
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
      this.updateTitle();
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
        case 'payments':
          return this.getPaymentDatasets();
        case 'credits':
          return this.getCreditDatasets();
        case 'mixed':
          return this.getMixedDatasets();
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
            } else {
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

            if (this.accumulated) {
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
    getPaymentDatasets() {
      let labels = [];
      let datasets = [];
      let lastYear = true;

      switch (this.periodName) {
        case 'monthly':
          let maxDays = 0;
          let monthIndex = this.month - 1;

          /**
           * Se recorren los pagos anuales
           */
          data.payments.forEach(annualPayment => {
            let payments = [];
            let dailyPayments = annualPayment.monthlyPayments[monthIndex].dailyPayments;
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.blue;

            /**
             * Se recuperan los abonos parciales o acumulados
             */
            if (this.accumulated) {
              payments = dailyPayments.map(p => p.accumulated);
            } else {
              payments = dailyPayments.map(p => p.partial);
            }

            /**
             * Ahora se define los días de las etiquetas
             */
            maxDays = maxDays >= payments.length ? maxDays : payments.length;

            /**
             * Finalmente se crea el dataset
             */
            let dataset = {
              label: annualPayment.year,
              backgroundColor: bgColor,
              borderColor: bgColor,
              borderWidth: 1,
              data: payments,
              fill: false
            }

            if (lastYear) {
              dataset.borderDash = [5, 5];
            }

            datasets.push(dataset);
            lastYear = false;
          }); //end forEach

          /**
           * Se crean las etiquetas
           */
          for (let i = 1; i <= maxDays; i++) {
            labels.push(i);
          }//end for
          break;
        case 'quarterly': {
          let tremester = this.tremester;

          /**
           * Recupero los datos anuales para 
           * crear los datasets
           */
          data.payments.forEach(annualPayment => {
            let payments = [];
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.blue;
            /**
             * Se recupera los meses que corresponden 
             * al trimestre actual
             */
            let monthlyPayments = annualPayment.monthlyPayments.filter(p => {
              let realTremester = p.month / 3.0;
              return realTremester > tremester - 1 && realTremester <= tremester;
            })

            /**
             * Se recuperan los pagos del trimestre
             */
            if (this.accumulated) {
              let accumulated = 0;
              payments = monthlyPayments.map(mp => {
                accumulated += mp.partial;
                return accumulated;
              })
            } else {
              payments = monthlyPayments.map(mp => mp.partial);
            }

            /**
             * Se crea el dataset
             */
            let dataset = {
              label: annualPayment.year,
              backgroundColor: window.color(bgColor).alpha(0.8).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: payments,
            }

            if (this.accumulated) {
              dataset.fill = false;
            }

            datasets.push(dataset);
            lastYear = false;
          })//end forEach

          data.months.forEach((monthName, index) => {
            let realTremester = (index + 1) / 3;
            let min = tremester - 1;
            if (min < realTremester && realTremester <= tremester) {
              labels.push(monthName);
            }
          })// end forEach
        } break;
        case 'biannual': {
          let semester = this.semester;

          /**
           * Recupero los datos anuales para 
           * crear los datasets
           */
          data.payments.forEach(annualPayment => {
            let payments = [];
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.blue;
            /**
             * Se recupera los meses que corresponden 
             * al trimestre actual
             */
            let monthlyPayments = annualPayment.monthlyPayments.filter(p => {
              let realSemester = p.month / 6.0;
              return realSemester > semester - 1 && realSemester <= semester;
            })

            /**
             * Se recuperan los pagos del trimestre
             */
            if (this.accumulated) {
              let accumulated = 0;
              payments = monthlyPayments.map(mp => {
                accumulated += mp.partial;
                return accumulated;
              })
            } else {
              payments = monthlyPayments.map(mp => mp.partial);
            }

            /**
             * Se crea el dataset
             */
            let dataset = {
              label: annualPayment.year,
              backgroundColor: window.color(bgColor).alpha(0.7).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: payments,
              fill: false,
            }

            datasets.push(dataset);
            lastYear = false;
          })//end forEach

          data.months.forEach((monthName, index) => {
            let realSemester = (index + 1) / 6.0;
            let min = semester - 1;
            if (min < realSemester && realSemester <= semester) {
              labels.push(monthName);
            }
          })// end forEach
        } break;
        case 'annual': {
          /**
           * Recupero los datos anuales para 
           * crear los datasets
           */
          data.payments.forEach(annualPayment => {
            let payments = [];
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.blue;
            /**
             * Se recupera los meses que corresponden 
             * al trimestre actual
             */
            let monthlyPayments = annualPayment.monthlyPayments;

            /**
             * Se recuperan los pagos del trimestre
             */
            if (this.accumulated) {
              payments = monthlyPayments.map(mp => mp.accumulated)
            } else {
              payments = monthlyPayments.map(mp => mp.partial);
            }

            /**
             * Se crea el dataset
             */
            let dataset = {
              label: annualPayment.year,
              backgroundColor: window.color(bgColor).alpha(0.7).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: payments,
              fill: false,
            }

            datasets.push(dataset);
            lastYear = false;
          })//end forEach

          labels = data.months;
        } break;
      }//end switch

      return { labels, datasets };
    },//end getPaymentDataset
    getCreditDatasets() {
      let labels = [];
      let datasets = [];
      let lastYear = true;

      switch (this.periodName) {
        case 'monthly':
          let maxDays = 0;
          let monthIndex = this.month - 1;

          /**
           * Se recorren los pagos anuales
           */
          data.credits.forEach(annualCredit => {
            let credits = [];
            let dailyCredits = annualCredit.monthlyCredits[monthIndex].dailyCredits;
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.blue;

            /**
             * Se recuperan los abonos parciales o acumulados
             */
            if (this.accumulated) {
              credits = dailyCredits.map(c => c.accumulated);
            } else {
              credits = dailyCredits.map(c => c.partial);
            }

            /**
             * Ahora se define los días de las etiquetas
             */
            maxDays = maxDays >= credits.length ? maxDays : credits.length;

            /**
             * Finalmente se crea el dataset
             */
            let dataset = {
              label: annualCredit.year,
              backgroundColor: bgColor,
              borderColor: bgColor,
              borderWidth: 1,
              data: credits,
              fill: false
            }

            if (lastYear) {
              dataset.borderDash = [5, 5];
            }

            datasets.push(dataset);
            lastYear = false;
          }); //end forEach

          /**
           * Se crean las etiquetas
           */
          for (let i = 1; i <= maxDays; i++) {
            labels.push(i);
          }//end for
          break;
        case 'quarterly': {
          let tremester = this.tremester;

          /**
           * Recupero los datos anuales para 
           * crear los datasets
           */
          data.credits.forEach(annualCredit => {
            let credits = [];
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.blue;
            /**
             * Se recupera los meses que corresponden 
             * al trimestre actual
             */
            let monthlyCredits = annualCredit.monthlyCredits.filter(p => {
              let realTremester = p.month / 3.0;
              return realTremester > tremester - 1 && realTremester <= tremester;
            })

            /**
             * Se recuperan los pagos del trimestre
             */
            if (this.accumulated) {
              let accumulated = 0;
              credits = monthlyCredits.map(mp => {
                accumulated += mp.partial;
                return accumulated;
              })
            } else {
              credits = monthlyCredits.map(mp => mp.partial);
            }

            /**
             * Se crea el dataset
             */
            let dataset = {
              label: annualCredit.year,
              backgroundColor: window.color(bgColor).alpha(0.8).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: credits,
            }

            if (this.accumulated) {
              dataset.fill = false;
            }

            datasets.push(dataset);
            lastYear = false;
          })//end forEach

          data.months.forEach((monthName, index) => {
            let realTremester = (index + 1) / 3;
            let min = tremester - 1;
            if (min < realTremester && realTremester <= tremester) {
              labels.push(monthName);
            }
          })// end forEach
        } break;
        case 'biannual': {
          let semester = this.semester;

          /**
           * Recupero los datos anuales para 
           * crear los datasets
           */
          data.credits.forEach(annualCredits => {
            let credits = [];
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.blue;
            /**
             * Se recupera los meses que corresponden 
             * al trimestre actual
             */
            let monthlyCredits = annualCredits.monthlyCredits.filter(p => {
              let realSemester = p.month / 6.0;
              return realSemester > semester - 1 && realSemester <= semester;
            })

            /**
             * Se recuperan los pagos del trimestre
             */
            if (this.accumulated) {
              let accumulated = 0;
              credits = monthlyCredits.map(mp => {
                accumulated += mp.partial;
                return accumulated;
              })
            } else {
              credits = monthlyCredits.map(mp => mp.partial);
            }

            /**
             * Se crea el dataset
             */
            let dataset = {
              label: annualCredits.year,
              backgroundColor: window.color(bgColor).alpha(0.7).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: credits,
              fill: false,
            }

            datasets.push(dataset);
            lastYear = false;
          })//end forEach

          data.months.forEach((monthName, index) => {
            let realSemester = (index + 1) / 6.0;
            let min = semester - 1;
            if (min < realSemester && realSemester <= semester) {
              labels.push(monthName);
            }
          })// end forEach
        } break;
        case 'annual': {
          /**
           * Recupero los datos anuales para 
           * crear los datasets
           */
          data.credits.forEach(annualCredits => {
            let credits = [];
            let bgColor = lastYear ? window.chartColors.red : window.chartColors.blue;
            /**
             * Se recupera los meses que corresponden 
             * al trimestre actual
             */
            let monthlyCredits = annualCredits.monthlyCredits;

            /**
             * Se recuperan los pagos del trimestre
             */
            if (this.accumulated) {
              credits = monthlyCredits.map(mp => mp.accumulated)
            } else {
              credits = monthlyCredits.map(mp => mp.partial);
            }

            /**
             * Se crea el dataset
             */
            let dataset = {
              label: annualCredits.year,
              backgroundColor: window.color(bgColor).alpha(0.7).rgbString(),
              borderColor: bgColor,
              borderWidth: 1,
              data: credits,
              fill: false,
            }

            datasets.push(dataset);
            lastYear = false;
          })//end forEach

          labels = data.months;
        } break;
      }//end switch

      return { labels, datasets };
    },//end getCreditDataset
    getMixedDatasets() {
      let labels = [];
      let datasets = [];

      let sales = this.getSaleDatasets();
      let credits = this.getCreditDatasets();
      let payments = this.getPaymentDatasets();

      labels = sales.labels;

      let salesDataset = this.showLastYear ? sales.datasets[0] : sales.datasets[1];
      let creditsDataset = this.showLastYear ? credits.datasets[0] : credits.datasets[1];
      let paymentsDataset = this.showLastYear ? payments.datasets[0] : payments.datasets[1];

      let saleColor = window.chartColors.green;
      let paymentColor = window.chartColors.blue;
      let creditColor = window.chartColors.red;

      salesDataset.label = "Ventas";
      creditsDataset.label = "Creditos";
      paymentsDataset.label = "Abonos";
      salesDataset.borderColor = saleColor;
      creditsDataset.borderColor = creditColor;
      paymentsDataset.borderColor = paymentColor;

      switch (this.periodName) {
        case 'monthly':
          salesDataset.backgroundColor = saleColor;
          creditsDataset.backgroundColor = creditColor;
          paymentsDataset.backgroundColor = paymentColor;
          break;
        case 'quarterly':
        case 'biannual':
        case 'annual':
          salesDataset.backgroundColor = window.color(saleColor).alpha(0.7).rgbString();
          creditsDataset.backgroundColor = window.color(creditColor).alpha(0.7).rgbString();
          paymentsDataset.backgroundColor = window.color(paymentColor).alpha(0.7).rgbString();
          break;
      }

      datasets = [salesDataset, paymentsDataset, creditsDataset];

      return {
        labels,
        datasets
      };
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

window.debtEvolutionModel = () => {
  return {
    tab: 'graph',
    periodName: 'monthly',
    year: 0,
    month: 0,
    title: '',
    init() {
      this.tab = 'graph';
      this.periodName = "monthly";
      this.month = data.month;
      this.year = data.year;
      this.title = `Evolucion de la deuda [${this.year}]`;
      this.updateChart();
    },
    changePeriodName() {
      if(this.periodName === 'monthly'){
        this.month = data.month;
      }

      this.updateChart();
    },
    updateChart() {
      let type = 'line';
      window.destroyCanvas('debtEvolution');
      let ctx = document.getElementById('debtEvolution');
      window.debtEvolutionChart = new Chart(ctx, {
        type,
        data: this.getDatasets(),
        options: this.getChartOptions(),
      })
    },
    getDatasets() {
      let labels = [0];
      let datasets = [];

      switch (this.periodName) {
        case 'monthly': {
          let monthIndex = this.month - 1;

          /**
           * Se recupera la evoucion de la deuda en
           * el mes seleccionado
           */
          let monthlyEvolution = data.debtEvolution.monthlyEvolution[monthIndex]
          let dailyEvolution = monthlyEvolution.dailyEvolution;

          /**
           * Se crea el conjunto de datos a mostrar
           */
          let debts = [monthlyEvolution.initialDebt];
          debts.push(...dailyEvolution.map(x => x.accumulated));

          /**
           * Ahora se crea las etiquetas
           */
          for (let i = 1; i <= dailyEvolution.length; i++) {
            labels.push(i);
          }

          /**
           * Se crea el dataset
           */
          datasets.push({
            label: data.months[monthIndex],
            backgroundColor: window.color(window.chartColors.blue).alpha(0.2).rgbString(),
            borderColor: window.chartColors.blue,
            borderWidth: 1,
            data: debts,
            fill: 'start'
          })
        } break;
        case 'annual': {
          let months = window.data.debtEvolution.monthlyEvolution.map(x => x.accumulated);
          let data = [window.data.debtEvolution.efectiveBalance, ...months];
          labels.push(...window.data.months);
          datasets.push({
            label: this.year,
            backgroundColor: window.color(window.chartColors.green).alpha(0.2).rgbString(),
            borderColor: window.chartColors.green,
            borderWidth: 1,
            data: data,
            fill: 'start'
          });
        } break;
      }

      return {
        labels,
        datasets
      }
    },
    getChartOptions() {
      return {
        resposive:true,
        legend: {
          position: 'top'
        },//end legend
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
            tension: 0.1
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
    },
    createUrl(id){
      return `${data.url}/${id}`;
    }
  };
}//end debtEvolution


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

  window.data.salesByCategories.forEach(category => {
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
  // customersDebtsBuild();
  salesByCategoriesBuild();
})