require('../utilities');
import Chart from 'chart.js/auto';


//*=============================================================================*
//*=================================== DAYJS ===================================*
//*=============================================================================*
//CONFIGURACIÓN DE DAYJS
const dayjs = require('dayjs');
require('dayjs/locale/es-do');

//Se adiciona el pluging para tiempo relativo
let relativeTime = require('dayjs/plugin/relativeTime');
dayjs.extend(relativeTime);

let isSameOrBefore = require('dayjs/plugin/isSameOrBefore');
dayjs.extend(isSameOrBefore);

let isSameOrAfter = require('dayjs/plugin/isSameOrAfter')
dayjs.extend(isSameOrAfter)

let localizedFormat = require('dayjs/plugin/localizedFormat')
dayjs.extend(localizedFormat)

//Se establece en español
dayjs.locale('es-do');

window.dayjs = dayjs;

window.app = () => {
  return {
    /** Listado de cajas del componente */
    boxs: [],
    /**  Guarda la instancia de la caja seleccionada */
    boxSelected: null,
    /** Sirve para determinar si una caja se está visualizando */
    boxActive: false,
    /** Arreglo de instancias de negocio */
    business: [],
    /** Objeto con los tipos de transacciones */
    transactionTypes: null,
    /** Encargado de realizar las peticiones al servidor */
    wire: undefined,
    /** Encargado de gestionar los eventos personalizados */
    dispatch: undefined,
    waiting: false,
    formActive: false,
    formTransactionActive: false,
    formClosingBoxActive: false,
    // *=========================================*
    // *======= METODOS DE INICIALIZACIÓN =======*
    // *=========================================*
    init(wire, dispatch) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.mountData();
    },
    /**
     * Se encarga de montar en el componente los datos
     * de cada una de las cajas
     */
    mountData() {
      this.waiting = true;
      this.wire.init()
        .then(res => {
          //Se construyen las cajas del modelo
          this.buildBoxs(res.boxs);
          //Se crean las instancias de los negocios
          this.buildBusiness(res.business);
          //Se crean los diversos tipos de transacciones
          this.transactionTypes = res.transactionTypes;
          this.buildGraphs();
          this.waiting = false;
        })
        .catch(error => console.log(error));
    },
    /**
     * Este metodo limpia la vista y vuelve a montar los datos 
     * provenientes desde el servidor.
     */
    resetComponent() {
      this.destroyGraphElements();
      this.boxs = [];
      this.boxSelected = null;
      this.boxActive = false;
      this.transactionTypes = null;
      this.business = [];
      this.waiting = false;
      this.formActive = false;
      this.formTransactionActive = false;
      this.formClosingBoxActive = false;
      this.mountData();
    },
    // *=========================================*
    // *============ FUNCIONALIDADES ============*
    // *=========================================*
    /**
     * Se encarga de la gestion de las cajas y de emitir un evento
     * para que el component encargado se encargue de adminsitrarlo.
     * @param {*} box Instancia de una caja
     */
    selectBox(box) {
      if (this.boxSelected) {
        this.boxSelected.selected = false;
      }
      this.boxSelected = box;
      this.boxSelected.selected = true;
      this.boxActive = true;
      this.dispatch('box-selected', { box });
    },
    /**
     * Este metodo se ejecuta cuando se cierra el panel de la caja
     * y de paso sirve para actualizar la grafica de dicha caja.
     */
    deselectBox() {
      this.boxSelected.selected = false;
      this.boxSelected = null;
      this.boxActive = false;
      //Espacio para actualizar graficas
    },
    hiddenBoxView() {
      let shop = this.business.find(b => b.name === this.boxSelected.business);
      if(shop){
        this.updateBusiness(shop);
        console.log(shop);
        this.updateGraph(shop);
      }
      this.deselectBox();
    },
    /**
     * Se ecnarga de mostar el formulario
     */
    enableTransactionForm() {
      this.formActive = true;
      this.enabledTransactionForm = true;
    },
    /**
     * Se encarga de ocultar el formulario
     */
    disableTransactionForm() {
      this.formActive = true;
      this.enableTransactionForm = false;
    },
    /**
     * Este metodo se encarga de agregar una nueva transacción a 
     * la caja actualmente seleccionada.
     * @param {*} data Datos de la transacción a agregar
     */
    addNewTransaction(data) {
      //Recupero la caja
      let box = this.boxs.find(b => b.id === data.box.id);
      //Creo la instancia de la transacción
      let transaction = this.processTransaction(data.transaction);
      //Se agrega al arreglo de la caja
      box.transactions.push(transaction);
      //Se ordena el listado por fecha
      box.transactions.sort((b1, b2) => {
        if (b1.date.isBefore(b2.date))
          return -1;
        if (b1.date.isAfter(b2.date))
          return 1;
        if (b1.date.isSame(b2.date))
          return 0;
      })
      //Se recalculan los parametros
      let parameters = this.getBoxParameters(box.transactions, box.closingDate);

      box.transactionsByType = parameters.transactionsByType;
      box.balance = parameters.balance;
      box.totalIncomes = parameters.totalIncomes;
      box.totalExpenses = parameters.totalExpenses;

      this.dispatch('new-transaction-added', { transaction });
    },
    /**
     * Este metodo se encarga de actualizar la información de 
     * una transacción recien modificada.
     * @param {*} data Instancia de una transacción
     */
    updateTransaction(data) {
      //Se recupera la caja
      let box = this.boxs.find(b => b.id === data.box.id);
      //Se recupera la transacción
      let transaction = box?.transactions.find(t => t.id === data.transaction.id);
      //Se actualizan los campos
      if (transaction) {
        for (const key in transaction) {
          if (Object.hasOwnProperty.call(data.transaction, key)) {
            transaction[key] = data.transaction[key];
          }
        }
      }

      //Se Actualizan los parametros
      let parameters = this.getBoxParameters(box.transactions, box.closingDate);

      box.transactionsByType = parameters.transactionsByType;
      box.balance = parameters.balance;
      box.totalIncomes = parameters.totalIncomes;
      box.totalExpenses = parameters.totalExpenses;

      //Se notifica que la transacción fue actualizada en la caja
      this.dispatch('transaction-updated', { box, transaction });
    },
    // *=========================================*
    // *========== CONTROL DE GRAFICAS ==========*
    // *=========================================*
    /**
     * Imprime las graficas de cada uno de los engocios en pantalla
     * y crea los datasets de los mismo
     */
    buildGraphs() {
      this.buildGraphElements();

      this.business.forEach(b => {
        //Se crea el archivo de configuracion
        let config = {
          type: 'line',
          data: this.getDatasets(b),
          options: this.getGraphOptions(),
        }
        //Se agrega el titulo de la grafica
        config.options.plugins.title.text = b.name;
        //Se crea la grafica
        let chart = new Chart(
          document.getElementById(b.uuid),
          config,
        );

        b.chart = chart;
      })
    },
    /**
     * Elimina la grafica del DOM para poder resetearla.
     * @param {*} business Instancia de negocio
     */
    updateGraph(business) {
      if(false){
        //Se crea el archivo de configuracion
        let config = {
          type: 'line',
          data: this.getDatasets(business),
          options: this.getGraphOptions()
        };
  
        //Se agrega el titulo a la grafica
        config.options.plugins.title.text = business.name;
  
        
        //Se elimina el canvas anterior
        document.getElementById(business.uuid).remove();
  
        //Se crea el canvas
        const canvas = document.createElement('canvas');
        canvas.id = business.uuid;
  
        //Se recupera el contenedor de la grafica
        const container = document.querySelector(`[data-id="${business.uuid}"]`);
  
        //Se inserta el canvas en el contenedor
        container.appendChild(canvas);
        
        // Se crea la grafica
        let chart = new Chart(canvas, config);
  
        business.chart = chart;
      }

      // console.log(business.chart.data.datasets);
      let datasets = business.chart.data.datasets;
      let maxItems = business.dailyReports.balances.length;
      for(let item = 0; item < maxItems; item++){
        datasets[0].data = [];
        datasets[0].data = business.dailyReports.incomes;
        
        datasets[1].data = [];
        datasets[1].data = business.dailyReports.expenses;
        
        datasets[2].data = [];
        datasets[2].data = business.dailyReports.balances;
        
        business.chart.update();
      }

    },
    /**
     * Se encarga de construir los elementos
     * donde son contenidos los graficos.
     */
    buildGraphElements() {
      const graphContainer = document.getElementById('graphContainer');

      this.business.forEach(b => {
        //Se construyen los elementos
        const container = document.createElement('div');
        const canvas = document.createElement('canvas');
        const card = document.createElement('div');
        const cardBody = document.createElement('div');

        //Se agregan las clases
        container.classList.add('col-12');
        card.classList.add('card');
        cardBody.classList.add('card-body');

        //Se agregan los atributos
        container.id = `container-${b.uuid}`;
        cardBody.setAttribute('data-id', b.uuid);
        canvas.id = b.uuid;

        //Se construye el arbol de dependencias
        graphContainer.appendChild(container);
        container.appendChild(card);
        card.appendChild(cardBody);
        cardBody.appendChild(canvas);
      })
    },
    /**
     * Se encarga de destruir todos los contenedores de las 
     * graficas para que puedan ser reseteadas.
     */
    destroyGraphElements() {
      this.business.forEach(b => {
        let id = `container-${b.uuid}`;
        // let attribute = `[data-id="${b.uuid}"]`
        document.getElementById(id).remove();
      })
    },
    getGraphOptions() {
      return {
        plugins: {
          title: {
            display: true,
            text: '',
            position: 'top',
            font: {
              size: 21,
            }
          },
          tooltip: {
            callbacks: {
              label: context => {
                let label = context.dataset.label || '';

                if (label) {
                  label += ': ';
                }

                if (context.parsed.y !== null) {
                  label += formatCurrency(context.parsed.y, 0);
                }

                return label;
              }, //end label
              title: context => {
                let dayOfMonth = context[0].parsed.x;
                let date = dayjs().startOf('month').add(dayOfMonth, 'day');
                return date.format('dddd DD [de] MMMM');
              }
            },
          }
        },
        scales: {
          yAxis: {
            beginAtZero: true,
            ticks: {
              callback: function (value, index, values) {
                return formatCurrency(value, 0);
              }//end callback
            },
            title: {
              display: true,
              text: 'Importe Acumulado',
              font: {
                size: 16
              }
            }
          },//.end yAxis
          xAxis: {
            title: {
              display: true,
              text: 'Días del mes',
              font: {
                size: 16
              }
            }
          },//.end xAxis
          // y1: {
          //   beginAtZero: true,
          //   position: 'right',
          //   title: {
          //     display: true,
          //     text: 'Saldo',
          //     font: {
          //       size: 16
          //     }
          //   },
          //   ticks: {
          //     callback: function (value, index, values) {
          //       return formatCurrency(value, 0);
          //     }//end callback
          //   },
          // }
        }
      }
    },
    /** 
     * Constuye los datasets del negocio pasado como parametro
     */
    getDatasets(business) {
      const labels = [];
      for (let day = 1; day <= dayjs().daysInMonth(); day++) {
        labels.push(day);
      }

      return {
        labels: labels,
        datasets: [{
          label: 'Ingresos',
          backgroundColor: 'rgb(75, 192, 192)',
          borderColor: 'rgb(75, 192, 192)',
          data: business.dailyReports.incomes,
          fill: false,
          tension: 0.3,
        },
        {
          label: 'Egresos',
          backgroundColor: 'rgb(255, 99, 132)',
          borderColor: 'rgb(255, 99, 132)',
          data: business.dailyReports.expenses,
          fill: false,
          tension: 0.3,
        },
        {
          label: 'Saldo Global',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          borderColor: 'rgba(54, 162, 235, 0.5)',
          data: business.dailyReports.balances,
          fill: true,
          tension: 0.4,
          // yAxisID: 'y1'
        }]
      };
    },
    /**
     * Metodo aparentemente innecesario
     */
    getBusinessData() {
      this.business.forEach(shop => {
        let balanceList = [];
        let incomesList = [];
        let expensesList = [];
        let balance = 0, incomes = 0, expense = 0;

        //Recupero las cajas del negocio
        let boxs = this.boxs.filter(box => box.business === shop.name);
        let transactions = [];
        //Combino las transacciones de todas las cajas, sin incluir las transfrencias
        boxs.forEach(box => {
          let filter = box.transactions.filter(t => t.type !== 'transfer');
          transactions = transactions.concat(filter);
        })

        //Recupero el saldo de las transacciones hasta el inicio del mes
        let start = dayjs().startOf('month');
        let now = dayjs();

        let lastTransacctions = transactions.filter(t => t.date.isBefore(start));
        let actualTransactions = transactions.filter(t => t.date.isSameOrAfter(start));

        balance = lastTransacctions.reduce((accumulator = 0, currentValue) => accumulator + currentValue.amount, 0);
        console.log(balance);

        //Se construyen las listas
        let secureCount = 0;
        while (start.isSameOrBefore(now)) {
          let end = dayjs(start).endOf('day');
          let dayTransactions = actualTransactions.filter(t => t.date.isSameOrAfter(start) && t.date.isSameOrBefore(end));
          dayTransactions.forEach(transaction => {
            balance += transaction.amount;
            if (transaction.amount > 0) {
              incomes += transaction.amount;
            } else {
              expense += transaction.amount * -1;
            }
          })

          balanceList.push(balance);
          incomesList.push(incomes);
          expensesList.push(expense);

          //Se aumenta la fecha en un día
          let format = 'YYYY-MM-DD HH:mm:ss';
          console.log(start.format(format), end.format(format), now.format(format));
          start = start.add(1, 'day');
          secureCount++;

          if (secureCount > 31) {
            console.log('Bucle infinito');
            break;
          }
          console.log(start.format(format), end.format(format), now.format(format));
          // break;
        }

        shop.incomes = incomesList;
        shop.expenses = expensesList;
        shop.balanceList = balanceList;
      })
    },
    // *==========================================*
    // *=============== UTILIDADES ===============*
    // *==========================================*
    /**
     * Construye la instancia de una caja de dinero, convitiendo las fechas en
     * instancias de dayjs
     * @param {*} data Datos de unca caja particular
     */
    buildBoxs(data) {
      data.forEach(record => {
        let closingDate = dayjs(record.closingDate);
        let transactions = [];
        record.transactions.forEach(t => {
          transactions.push(this.processTransaction(t));
        })

        let parameters = this.getBoxParameters(transactions, closingDate);

        this.boxs.push({
          id: record.id,
          name: record.name,
          business: record.business,
          cashier: record.cashier,
          main: record.main,
          closingDate,
          closingDateFormatted: closingDate.format('LLL'),
          closingDateRelative: closingDate.fromNow(),
          base: record.base,
          transactions: transactions,
          transactionsByType: parameters.transactionsByType,
          balance: parameters.balance,
          totalIncomes: parameters.totalIncomes,
          totalExpenses: parameters.totalExpenses,
          selected: false,
        })
      });
    },
    /**
     * Se encarga de formatear las fechas del objeto y calcula el saldo de la caja
     * @param {array} data Arreglo con los datos de las transacciónes
     * @param {Dayjs} closingDate Fecha del ultimo corte
     * @returns Transacciones e importe total
     */
    processTransactions(data, closingDate) {
      let transactions = [];

      data.forEach(record => {
        transactions.push(this.processTransaction(record));
      });

      let parameters = this.getBoxParameters(transactions,)

      return {
        transactions,
        transactionsByType,
        balance,
        totalIncomes,
        totalExpenses
      }
    },
    processTransaction(data) {
      return {
        id: data.id,
        date: dayjs(data.date),
        description: data.description,
        type: data.type,
        amount: data.amount,
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
      }
    },
    getBoxParameters(transactions, closingDate) {
      let balance = 0;
      let totalIncomes = 0;
      let totalExpenses = 0;

      let transactionsByType = {
        general: { income: 0, expense: 0, transactions: [] },
        sale: { income: 0, expense: 0, transactions: [] },
        expense: { income: 0, expense: 0, transactions: [] },
        purchase: { income: 0, expense: 0, transactions: [] },
        service: { income: 0, expense: 0, transactions: [] },
        credit: { income: 0, expense: 0, transactions: [] },
        payment: { income: 0, expense: 0, transactions: [] },
        transfer: { income: 0, expense: 0, transactions: [] },
      }

      transactions.forEach(transaction => {
        //Se actualizan las variables financieras si la fecha es mayor o igual a la fecha de cierre
        if (transaction.date.isSameOrAfter(closingDate)) {
          if (Object.hasOwnProperty.call(transactionsByType, transaction.type)) {
            //Se define el valor absoluto de la transacción
            let amount = Math.abs(transaction.amount);
            if (transaction.amount > 0) {
              totalIncomes += amount;
              transactionsByType[transaction.type].income += amount;
              transactionsByType[transaction.type].transactions.push(transaction);
            } else {
              totalExpenses += amount;
              transactionsByType[transaction.type].expense += amount;
              transactionsByType[transaction.type].transactions.push(transaction);
            }
          }
        }

        balance += transaction.amount
      });

      return {
        transactionsByType,
        balance,
        totalIncomes,
        totalExpenses
      }
    },
    /**
     * Construye las instancias de los negocios del sistema, crea los reportes diarios
     * de ingresos, egresos y saldos
     * @param {*} data Información de cada negocio registrado en la plataforma
     */
    buildBusiness(data) {
      //Para cada elmento se cea una instancia
      data.forEach(record => {
        let id = record.id;
        let name = record.name;
        let uuid = record.uuid;
        //Se recuperan las cajas que pertenecen al negocio
        let boxs = this.boxs.filter(box => box.business === name);
        //Se recuperan todas las transacciones sin incluir las transferencias
        let transactions = [];
        boxs.forEach(box => {
          let boxTransactions = box.transactions.filter(t => t.type !== 'transfer');
          transactions = transactions.concat(boxTransactions);
        })

        //Se recupera el saldo de las transacciones anteriores al mes actual


        this.business.push({
          id,
          name,
          uuid,
          boxs,
          transactions,
          dailyReports: this.getDailyReports(transactions)
        })

      })
    },
    /**
     * Actualiza el arreglo de transacciones y el objeto de reportes diarios.
     * @param {*} business Instanica de negocio
     */
    updateBusiness(business) {
      //Se actualizan las transacciones
      let transactions = [];
      business.boxs.forEach(box => {
        let boxTransactions = box.transactions.filter(t => t.type !== 'transfer');
        transactions = transactions.concat(boxTransactions);
      });

      business.dailyReports = this.getDailyReports(transactions);
    },
    /**
     * Se encarga de construir los reportes diarios de ingresos, egresos y saldo
     * hasta el día actual
     * @param {*} balance El saldo del negocio antes del mes actual
     * @param {*} transactions Arreglo con las transacciones del mes actual
     */
    getDailyReports(transactions) {
      let incomes = 0;                        //Ingresos acumulados del mes
      let expenses = 0;                       //Egresos acumulados del mes
      let balance = 0;
      let now = dayjs();                      //Limite del ciclo
      let start = dayjs().startOf('month');   //Inicio de cada ciclo
      let end = dayjs(start).endOf('day');    //Fin de cada ciclo
      const maxMonthCicle = 31;               //Numero maximo de repeticiones del ciclo
      let cicleCount = 1;                     //Conteo de repeticiones

      // let balance = transactions.filter(transaction => transaction.date.isBefore(start))
      //   .reduce((accumulator, transaction) => accumulator + transaction.amount, 0);
      let actualTransactions = transactions.filter(t => t.date.isSameOrAfter(start));

      /**
       * Guarda los reportes diarios de los ingresos, egresos y saldos
       */
      let dailyReports = {
        balances: [],
        incomes: [],
        expenses: []
      }

      while (start.isSameOrBefore(now)) {
        // incomes = 0;
        // expenses = 0;
        //Recupero las transacciones del día
        let dailyTransactions = actualTransactions.filter(t => t.date.isSameOrAfter(start) && t.date.isSameOrBefore(end))
        //Actualizo las variables acumulativas
        dailyTransactions.forEach(t => {
          balance += t.amount;
          if (t.amount > 0) {
            incomes += t.amount;
          } else {
            expenses += t.amount * -1;
          }
        })

        //Ahora se actualiza el reporte diario
        dailyReports.balances.push(balance);
        dailyReports.incomes.push(incomes);
        dailyReports.expenses.push(expenses);

        //Se actualizan las variables del bulce
        start = start.add(1, 'day');
        end = end.add(1, 'day');
        cicleCount++;

        if (cicleCount > maxMonthCicle) {
          break;
        }
      }

      return dailyReports;

    }
  }
}

require('./boxComponent');
require('./formComponent');



window.addEventListener('load', () => {
  document.querySelector('.content-header').remove();
})