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
    businessSelected: 0,
    graphs: {
      general: null,
      incomes: null,
      expenses: null,
    },
    graphPeriod: 'this-month',
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
      this.buildGeneralGraph();
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
          // this.buildGraphs();
          this.updateStatistics();
          this.waiting = false;
        })
        .catch(error => console.log(error));
    },
    /**
     * Este metodo limpia la vista y vuelve a montar los datos 
     * provenientes desde el servidor.
     */
    resetComponent() {
      // this.destroyGraphElements();
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
    },
    hiddenBoxView() {
      let shop = this.business.find(b => b.name === this.boxSelected.business);
      if (shop) {
        this.updateBusiness(shop);
        if(shop.id === this.businessSelected){
          this.updateStatistics();
        }
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

      //Se agrega la transaccion en el negocio
      let deal = this.business.find(b => b.name === box.business);
      deal.transactions.push(transaction);

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
      let deal = this.business.find(b => b.name === box.business);
      //Se recupera la transacción
      let transaction = box?.transactions.find(t => t.id === data.transaction.id);
      let businessTransaction = deal.transactions.find(t => t.id === data.transaction.id);
      //Se actualizan los campos
      if (transaction) {
        for (const key in transaction) {
          if (Object.hasOwnProperty.call(data.transaction, key)) {
            transaction[key] = data.transaction[key];
            businessTransaction[key] = data.transaction[key];
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
    },
    buildGeneralGraph() {
      let canvas = document.getElementById('generalGraph');
      let type = 'line';
      let options = {
        plugins: {
          title: {
            display: true,
            text: 'Estadisticas Generales',
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
              // title: context => {
              //   let dayOfMonth = context[0].parsed.x;
              //   let date = dayjs().startOf('month').add(dayOfMonth, 'day');
              //   return date.format('dddd DD [de] MMMM');
              // }
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
              display: false,
              text: 'Importe Acumulado',
              font: {
                size: 16
              }
            }
          },//.end yAxis
          xAxis: {
            title: {
              display: false,
              text: 'Días del mes',
              font: {
                size: 16
              }
            }
          },
          // }
        }
      }

      let datasets = [{
        label: 'Ingresos',
        backgroundColor: 'rgb(75, 192, 192)',
        borderColor: 'rgb(75, 192, 192)',
        data: [],
        fill: false,
        tension: 0.3,
      },
      {
        label: 'Egresos',
        backgroundColor: 'rgb(255, 99, 132)',
        borderColor: 'rgb(255, 99, 132)',
        data: [],
        fill: false,
        tension: 0.3,
      },
      {
        label: 'Saldo',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 0.5)',
        data: [],
        fill: true,
        tension: 0.4,
        // yAxisID: 'y1'
      }];


      this.graphs.general = new Chart(canvas, {
        type,
        options,
        data: {
          labels: [],
          datasets
        }
      })
    },
    updateStatistics() {
      let period = this.graphPeriod;
      let statistics = {};

      if (this.businessSelected) {
        let deal = this.business.find(b => b.id === this.businessSelected);
        let startDate = dayjs(), endDate = dayjs();
        let monthlyReport = false;

        if (period === 'this-month') {
          startDate = startDate.startOf('month');
        } else if (period === 'last-month') {
          startDate = startDate.subtract(1, 'month').startOf('month');
          endDate = dayjs(startDate).endOf('month');
        }else if(period === 'all-months'){
          startDate = startDate.startOf('year');
          monthlyReport = true;
        }

        statistics = this.getStatistics(deal.transactions, startDate, endDate, monthlyReport);
        this.updateGeneralGraph(statistics.labels, statistics.incomes, statistics.expenses, statistics.balances);
      }

    },
    updateGeneralGraph(labels, incomes, expenses, balances) {
      //Recupero el negocio
      let generalGraph = this.graphs.general;
      generalGraph.data.labels = labels;
      generalGraph.data.datasets[0].data = incomes;
      generalGraph.data.datasets[1].data = expenses;
      generalGraph.data.datasets[2].data = balances;
      generalGraph.update();

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
          // dailyReports: this.getDailyReports(transactions)
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

      // business.dailyReports = this.getDailyReports(transactions);
    },
    /**
     * Construye los datos que son consumidos por las graficas
     * @param {*} transactions Arreglo con las transacciones de un negocio sin incluir transferencias
     * @param {*} startDate Instacia de dayjs con la fecha en la que inica la busqueda
     * @param {*} endDate Instacia de dayjs en la que debe terminar la busqueda
     * @param {*} monthly Si los reportes son diarios o mensuales
     */
    getStatistics(transactions, startDate, endDate, monthly = false) {
      let income = 0, expense = 0, balance = 0;
      let incomes = [], expenses = [], balances = [];
      let labels = [];

      let start = dayjs(startDate).startOf('day');
      let end = monthly ? dayjs(start).endOf('month') : dayjs(start).endOf('day');

      while (start.isSameOrBefore(endDate)) {
        //Recpero las transacciones que están dentro del arreglo
        let transactionGroup = transactions.filter(t => t.date.isSameOrAfter(start) && t.date.isSameOrBefore(end));
        //Actualizo los saldos
        transactionGroup.forEach(transaction => {
          balance += transaction.amount;
          income += transaction.amount > 0 ? transaction.amount : 0;
          expense += transaction.amount < 0 ? transaction.amount * -1 : 0;
        });

        //Se agrega la etiqueta
        if(monthly){
          labels.push(start.format('MMMM'));
        }else{
          labels.push(start.format('ddd DD'));
        }

        //Se agrega el dato
        incomes.push(income);
        expenses.push(expense);
        balances.push(balance);

        //Se actualizan las fechas
        start = monthly ? start.add(1, 'month').startOf('month') : start.add(1, 'day');
        end = monthly ? dayjs(start).endOf('month') : dayjs(start).endOf('day');
      }

      return {
        labels,
        incomes,
        expenses,
        balances
      }
    },
  }
}

require('./boxComponent');
require('./formComponent');



window.addEventListener('load', () => {
  document.querySelector('.content-header').remove();
})