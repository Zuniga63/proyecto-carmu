window.boxComponent = () => {
  return {
    tab: 'info',        //[info, transactions]
    box: {},            //Caja del componente
    transactions: [],   //Transacciones filtradas 
    closingBox: false,
    banknotes: null,
    bankCoins: null,
    wire: undefined,
    dispatch: undefined,
    refs: undefined,
    dateFormat: 'DD-MM-YYYY',
    // *=========================================*
    // *======= METODOS DE INICIALIZACIÓN =======*
    // *=========================================*
    init(wire, dispatch, refs) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;
      this.buildPaperMoney();
    },
    mountBox(box) {
      this.tab = 'info';
      this.box = box;
      this.transactions = [];
    },
    buildPaperMoney() {
      this.banknotes = {
        thousand: {
          count: 0,
          value: 1000,
          amount: 0,
        },
        twoThousand: {
          count: 0,
          value: 2000,
          amount: 0,
        },
        fiveThousand: {
          count: 0,
          value: 5000,
          amount: 0,
        },
        tenThousand: {
          count: 0,
          value: 10000,
          amount: 0,
        },
        twentyThousand: {
          count: 0,
          value: 20000,
          amount: 0,
        },
        fiftyThousand: {
          count: 0,
          value: 50000,
          amount: 0,
        },
        hundredThousand: {
          count: 0,
          value: 100000,
          amount: 0,
        },
      };
      this.bankCoins = {
        hundred: {
          count: 0,
          value: 100,
          amount: 0
        },
        twoHundred: {
          count: 0,
          value: 200,
          amount: 0
        },
        fiveHundred: {
          count: 0,
          value: 500,
          amount: 0
        },
        Thousand: {
          count: 0,
          value: 1000,
          amount: 0
        },
      };

      this.refs.bankCoinsAmount.innerText = window.formatCurrency(0,0);
      this.refs.banknotesAmount.innerText = window.formatCurrency(0,0);
    },
    showTransactions() {
      this.tab = 'transactions';
      let balance = this.box.base;
      if (this.box.transactions.length > 0 && this.transactions.length === 0) {
        this.box.transactions.forEach(t => {
          if (t.date.isSameOrAfter(this.box.closingDate)) {
            balance += t.amount;
            t.balance = balance;
            this.transactions.push(t);
          }
        })
      }
    },
    changeTab(value) {
      this.dispatch('disable-form');
      this.tab = value;
      if (value === 'transactions') {
        this.closingBox = false;
        // this.enableTransactionForm();
        this.showTransactions();
      }
    },
    /**
     * Se encarga de notificarle al componente principal
     * que habilite el formulario para nuevas transacciones
     * @param {*} transactionData Instancia de una transacción
     */
    enableTransactionForm(transactionData = null) {
      this.dispatch('enable-form', {
        mode: 'transaction',
        transaction: transactionData,
        box: this.box
      });
    },
    addNewTransaction(transaction){
      let count = this.transactions.length;
      let balance = count > 0 ? this.transactions[count -1].balance : 0;
      transaction.balance = balance + transaction.amount;
      this.transactions.push(transaction);
    },
    updateTransaction(data){
      if(data.box.id === this.box.id){
        this.transactions = [];
        this.showTransactions();
      }
    },
    enableBoxClosing() {
      this.dispatch('enable-form', {
        mode: 'closing-box',
        box: this.box
      });

      this.closingBox = true;
    },
    disableTransactionForm() {
      this.dispatch('disable-form');
    },
    updateAmounts(){
      let banknotesAmount = 0;
      let bankCoinsAmount = 0;

      for(const [key, bill] of Object.entries(this.banknotes)){
        bill.amount = parseInt(bill.count) * bill.value;
        banknotesAmount += bill.amount;
      }

      for(const [key, coin] of Object.entries(this.bankCoins)){
        coin.amount = parseInt(coin.count) * coin.value;
        bankCoinsAmount += coin.amount;
      }

      let cashRegister = bankCoinsAmount + banknotesAmount;

      this.dispatch('new-cash-register', {cashRegister});
      this.refs.bankCoinsAmount.innerText = window.formatCurrency(bankCoinsAmount,0);
      this.refs.banknotesAmount.innerText = window.formatCurrency(banknotesAmount,0);
    },
    hiddenComponent(){
      this.closingBox = false;
      this.dispatch('hidden-box-view');
      this.disableTransactionForm();
      this.closingBox = false;
    }

  }
}