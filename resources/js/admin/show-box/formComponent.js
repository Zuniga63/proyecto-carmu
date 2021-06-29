import dayjs from 'dayjs';
import input from '../input';

window.formComponent = () => {
  return {
    state: 'register',
    /** Instancia de la caja que se desea modificar */
    box: null,
    originalTransaction: null,
    // *===========================================*
    // *======= CAMPOS DE LAS TRANSACCIONES =======*
    // *===========================================*
    /** Tipo de transacción de los seis que existen */
    transactionType: null,
    /** Determina el momento de la transacción */
    moment: null,
    /** Fecha de la transacción en formato YYYY-MM-DD */
    transactionDate: null,
    /** Habilita o deshabilita el ingreso de la hora */
    setTime: false,
    /** Hora de la transacción en formato HH:mm */
    transactionTime: null,
    /** Descripción del movimiento */
    description: null,
    /** Importe de la transacción en pesos colombianos */
    transactionAmount: null,
    /** Para las transacciones generales, define si es ingreso o egresos */
    amountType: 'income',
    // *===========================================*
    // *======== CAMPOS DEL CIERRE DE CAJA ========*
    // *===========================================*
    /** Es el dinero contado en la caja registradora */
    cashRegister: 0,
    /** Es el dinero faltante de la caja registradora */
    missingCash: 0,
    /** Es el dinero sobrante de la caja registradora */
    leftoverCash: 0,
    /** Es el valor de la nueva base */
    newBase: null,
    cashReplenishment: 0,
    cashTransfer: 0,
    /** Determina si el componente está esperando la respuestas del servidor */
    waiting: false,
    /** Encargado de las peticiones al servidor */
    wire: undefined,
    /** Encargado de emitir los eventos */
    dispatch: undefined,
    /** Encargado de facilitar el acceso a las referencias del DOM */
    refs: undefined,
    // *=========================================*
    // *======= METODOS DE INICIALIZACIÓN =======*
    // *=========================================*
    init(wire, dispatch, refs) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;
      this.buildInputs();
    },
    /**
     * Se encarga de crear las instancias 
     * que controlan los campos del formulario
     */
    buildInputs() {
      this.transactionType = input({
        id: 'transaction-type',
        name: 'transactionType',
        label: 'Tipo de transacción',
        required: true,
        value: 'general'
      });

      this.moment = input({
        id: 'transaction-moment',
        name: 'moment',
        label: 'Momento de la transación',
        require: true,
        value: 'now',
      });

      this.transactionDate = input({
        id: 'transaction-date',
        name: 'date',
        label: 'selecciona una fecha',
        required: true,
        min: null,
        max: dayjs().format('YYYY-MM-DD'),
      });

      this.transactionTime = input({
        id: 'transaction-time',
        name: 'time',
        label: 'Hora:',
        required: true,
      });

      this.description = input({
        id: 'transaction-description',
        name: 'description',
        label: 'Descripción',
        placeholder: 'Escribe los detalles aquí',
        required: true,
        min: 3,
        max: 255,
      });

      this.transactionAmount = input({
        id: 'transaction-amount',
        name: 'amount',
        label: 'Importe',
        required: true,
        placeholder: '$ 0.00',
        min: 100,
        max: 100000000,
      });

      this.newBase = input({
        id: 'closing-new-base',
        name: 'newBase',
        label: 'Nueva Base',
        required: true,
        placeholder: '$ 0.00',
        min: 0,
        max: 100000000,
      });
    },
    reset() {
      this.state = 'register';
      // this.box = null;
      this.originalTransaction = null;
      this.waiting = false;

      //Se resetea los campos de las transacciones
      this.transactionType.reset();
      this.moment.reset();
      this.description.reset();
      this.transactionAmount.reset();
      this.amountType = 'income';
      if (this.refs.transactionAmount) {
        this.refs.transactionAmount.value = '';
      }

      //Se resetean los campos del cierre de caja
      this.cashRegister = 0;
      this.missingCash = 0;
      this.leftoverCash = 0;
      this.newBase.reset();
      this.cashReplenishment = 0;
      this.cashTransfer = 0;

      if (this.refs.newBase) {
        this.refs.newBase.value = '';
      }
    },
    enableForm(data) {
      this.reset();
      this.box = data.box;

      if (data.mode === 'transaction') {
        if (data.transaction) {
          this.state = 'upgrade';
          this.mountTransaction(data.transaction);
        } else {
          this.state = 'register';
        }
        this.transactionDate.min = this.box.closingDate.format('YYYY-MM-DD');
      } else if (data.mode = 'closing-box') {
        this.state = 'closing-box';
        this.newBase.value = data.box.base;
        this.updateClosingParameters();
        setTimeout(() => {
          this.refs.newBase.value = window.formatCurrency(data.box.base, 0);
        }, 500);
      }
    },
    mountTransaction(transaction) {
      let amountAbs = Math.abs(transaction.amount);

      this.originalTransaction = transaction;
      this.transactionType.value = transaction.type;
      this.moment.value = 'other';
      this.transactionDate.value = transaction.date.format('YYYY-MM-DD');
      this.setTime = true;
      this.transactionTime.value = transaction.date.format('HH:mm');
      this.description.value = transaction.description;
      this.transactionAmount.value = amountAbs;
      if (transaction.type === 'general') {
        if (transaction.amount > 0) {
          this.amountType = 'income';
        } else {
          this.amountType = 'expense';
        }
      }

      this.refs.transactionAmount.value = window.formatCurrency(amountAbs, 0);
    },
    // *===========================================*
    // *============= FUNCIONALIDADES =============*
    // *===========================================*
    submit() {
      if (this.state === 'register') {
        this.storeTransaction();
      } else if (this.state === 'upgrade') {
        this.updateTransaction();
      }else if(this.state === 'closing-box'){
        this.storeClosingBox();
      }
    },
    storeTransaction() {
      if (this.validateTransaction()) {
        //Se recuperan los datos
        let data = this.getTransactionData();
        this.waiting = true;
        this.wire.storeTransaction(data)
          .then(res => {
            if (res.ok) {
              this.printSubmitData(res.transaction);
              this.dispatch('new-transaction', {
                box: this.box,
                transaction: res.transaction
              });
              this.reset();
            } else {
              this.printSubmitData(res.errors);
            }
          }).catch(error => {
            console.log(error);
          }).finally(() => {
            this.waiting = false;
          })
      }
    },
    updateTransaction() {
      if (this.validateTransaction()) {
        let data = this.getTransactionData();
        this.printSubmitData(data);
        this.waiting = true;
        this.wire.updateTransaction(data)
        .then(res => { 
          if(res.ok){
            let transaction = this.processTransactionData(res.transaction);
            //Dispara el evento para que el componente principal
            //Se encague de actualizar la transacción correspondiente
            this.dispatch('update-transaction', {
              box: this.box,
              transaction
            });
            
            this.reset();
          }else{
            console.log(res.errors);
          }
        })
        .catch(error => { 
          console.log(error);
        })
        .finally(() => {
          this.waiting = false;
        })
      }
    },
    updateRegisterCash(newValue) {
      this.cashRegister = newValue;
      this.updateClosingParameters();
    },
    updateClosingParameters() {
      let boxBalance = this.box.balance;
      let cashRegister = this.cashRegister;
      let newBase = this.newBase.value || 0;
      let diff = cashRegister - boxBalance;

      if (diff > 0) {
        this.leftoverCash = diff;
        this.missingCash = 0;
      } else if (diff < 0) {
        this.missingCash = Math.abs(diff);
        this.leftoverCash = 0;
      } else {
        this.missingCash = 0;
        this.leftoverCash = 0;
      }

      if (newBase > cashRegister) {
        this.cashReplenishment = newBase - cashRegister;
        this.cashTransfer = 0;
      } else if (newBase < cashRegister) {
        this.cashTransfer = cashRegister - newBase;
        this.cashReplenishment = 0;
      } else {
        this.cashTransfer = 0;
        this.cashReplenishment = 0;
      }

    },
    getTransactionData() {
      //Se establecen las variables
      let data = null;
      if (this.state === 'register' || this.state === 'upgrade') {
        data = {};
        //Campos obligatorios
        data.boxId = this.box.id;
        data.type = this.transactionType.value;
        data.description = this.description.value;
        data.amount = this.transactionAmount.value;
        data.amountType = data.type === 'general' ? this.amountType : null;

        data.moment = this.moment.value;
        data.setTime = this.setTime;
        if (data.moment === 'other') {
          data.date = this.transactionDate.value;
          data.time = this.setTime ? this.transactionTime.value : undefined;
        }
        if (this.state === 'upgrade') {
          data.transactionId = this.originalTransaction.id;
        }
      }else if(this.state === 'closing-box'){
        data = {};
        data.boxId = this.box.id;
        data.cashRegister = this.cashRegister;
        data.leftoverCash = this.leftoverCash;
        data.missingCash = this.missingCash;
        data.newBase = this.newBase.value;
        data.cashReplenishment = this.cashReplenishment;
        data.cashTransfer = this.cashTransfer;
      }

      return data;
    },
    storeClosingBox(){
      if(this.validateNewBase()){
        let data = this.getTransactionData();
        this.waiting = true;
        this.wire.storeClosingBox(data)
          .then(res => {
            if(res.ok){
              this.dispatch('box-closed');
            }else{
              this.printSubmitData(res.errors);
            }
          })
          .catch(error => {
            console.log(error);
          })
          .finally(()=>{
            this.waiting = false;
          })
      }
    },
    // *============================================*
    // *=============== VALIDACIONES ===============*
    // *============  ================================*
    /**
     * Se encarga de verificar que el tipo de transacción corresponda
     * a un tipo valido.
     * @returns {boolean}
     */
    validateTransactionType() {
      let isOk = false;
      let types = ['general', 'sale', 'expense', 'purchase', 'service', 'credit', 'payment'];
      let type = this.transactionType.value;
      if (types.some(val => val === type)) {
        isOk = true;
        this.transactionType.isOk();
      } else {
        this.transactionType.setError('El tipo de transacción es inválido');
      }

      return isOk;
    },
    validateMoment() {
      let isOk = false;
      if (this.moment.value === 'now' || this.moment.value === 'other') {
        this.moment.isOk();
        isOk = true;
      } else {
        this.moment.setError('El momento seleccionado es incorrecto');
      }

      return isOk;
    },
    validateDate() {
      let isOk = false;
      let date = this.transactionDate.value;
      let message = '';
      if (this.moment.value === 'other') {
        //No es null y no es un string vacío
        if (date && date.length > 0) {
          //Se crean las varibles tempórales
          let min = dayjs(this.transactionDate.min);
          let max = dayjs(this.transactionDate.max);
          date = dayjs(date);
          //¿La fecha es posterior a la fecha minima?
          if (date.isSameOrAfter(min)) {
            //¿La fecha es anterior a la fecha maxima?
            if (date.isSameOrBefore(max)) {
              this.transactionDate.isOk();
              isOk = true;
              //Se valida la hora si está estabecidad manualmente
              if (this.setTime) {
                this.validateTime();
              }
            } else {
              message = "La fecha es posterior al día de hoy";
            }
          } else {
            message = "La fecha es anterior a la fecha de corte";
          }
        } else {
          message = 'Se debe seleccionar una fecha';
        }
      } else {
        isOk = true;
      }

      if (!isOk) {
        this.transactionDate.setError(message);
      }
    },
    validateTime() {
      let isOk = false;
      let time = this.transactionTime.value;
      let date = this.transactionDate.value;
      let message = null;

      //¿la hora se establece de forma manual?
      if (this.moment.value === 'other' && this.setTime) {
        //¿La fecha ya fue establecida?
        if (date && !this.transactionDate.hasError) {
          //¿La hora ya fue seleccionada?
          if (time && time.length > 0) {
            //Se crean las variables temporales
            let min = dayjs(this.box.closingDate);
            let max = dayjs();
            let fullDate = dayjs(`${date} ${time}`);
            //¿Es la fecha posterior a la fecha de cierre?
            if (fullDate.isSameOrAfter(min)) {
              //¿Es la fecha anterior o igual al momento actual?
              if (fullDate.isSameOrBefore(max)) {
                this.transactionTime.isOk();
                isOk = true;
              } else {
                message = "La fecha y hora es posterior al momento presente";
              }
            } else {
              message = "La combinacion de fecha y hora es anterior a la fecha de corte";
            }
          } else {
            message = "¡Debes seleccionar una hora!";
          }
        } else {
          message = "Se debe seleccionar una fecha.";
        }
      } else {
        //La hora se define automaticamente
        isOk = true;
      }

      if (!isOk) {
        this.transactionTime.setError(message);
      }

      return isOk;
    },
    validateDescription() {
      let value = this.description.value;
      let ok = false;
      let message = null;

      if (value && value.length > 0) {
        if (value.length >= this.description.min) {
          if (value.length <= this.description.max) {
            this.description.isOk();
            ok = true;
          } else {
            message = "La descripción es demasiado larga";
          }
        } else {
          message = "La descripción es demasiado corta";
        }
      } else {
        message = "Este campo es requerido";
      }

      if (!ok) {
        this.description.setError(message);
      }

      return ok;
    },
    validateTransactionAmount() {
      let result = this.validateCurrency(this.transactionAmount.value, this.transactionAmount.min, this.transactionAmount.max);
      if (!result.ok) {
        this.transactionAmount.setError(result.message);
      }else{
        this.transactionAmount.isOk();
      }
      return result.ok;
    },
    validateNewBase() {
      let result = this.validateCurrency(this.newBase.value, this.newBase.min, this.newBase.max);
      if (!result.ok) {
        this.newBase.setError(result.message);
      }else{
        this.newBase.isOk();
      }
      return result.ok;
    },
    validateCurrency(value, min, max) {
      let ok = false;
      let message = null;
      value = parseInt(value);

      if (!isNaN(value) && value >= 0) {
        if (value >= min) {
          if (value < max) {
            ok = true;
            this.transactionAmount.isOk();
          } else {
            message = "El importe debe ser inferior que " + window.formatCurrency(max, 0);
          }
        } else {
          message = "El importe debe ser mayor a " + window.formatCurrency(min, 0);
        }
      } else {
        message = "Este campo es requerido";
      }

      return { ok, message };
    },
    validateTransaction() {
      let validations = [];
      validations.push(
        this.validateTransactionType(),
        this.validateMoment(),
        this.validateDescription(),
        this.validateTransactionAmount()
      );

      if (!this.moment.hasError && this.moment.value === 'other') {
        validations.push(this.validateDate());
        validations.push(this.validateTime());
      }

      return !validations.some(val => val === false);
    },
    // *==========================================*
    // *=============== UTILIDADES ===============*
    // *==========================================*
    /**
     * Metodo requerido para validar y guardar de forma correcta el valor del 
     * importe de la transacción ya que este en la vista requiere ser formateado.
     * utiliza el objeto refs para acceder el elemento del DOM
     */
    formatAmount(target, inputRef) {
      let value = window.deleteCurrencyFormat(target.value);
      this.refs[inputRef].value = window.formatCurrency(value, 0);
      if (inputRef === 'newBase') {
        this.newBase.value = value;
        this.updateClosingParameters();
        this.validateNewBase();
      } else if (inputRef === 'transactionAmount') {
        this.transactionAmount.value = value;
        this.validateTransactionAmount();
      }
    },
    __errorsTest() {
      let message = "Error de pruebas N°1"
      let timeout = 1000;
      setTimeout(() => {
        this.transactionType.setError(message);
      }, timeout);

      setTimeout(() => {
        this.moment.setError(message);
        this.moment.value = 'other';
      }, timeout * 2);

      setTimeout(() => {
        this.transactionDate.setError(message);
        this.setTime = true;
      }, timeout * 3);
      setTimeout(() => {
        this.transactionTime.setError(message);
      }, timeout * 4);
      setTimeout(() => {
        this.description.setError(message);
      }, timeout * 5);
      setTimeout(() => {
        this.transactionAmount.setError(message);
      }, timeout * 6);

    },
    printSubmitData(data) {
      let bodyLength = 150;
      let separator = '-';
      let header = '';
      let left = `+${separator}`;
      let right = `${separator}+`;
      let text = '';

      header = left + separator.repeat(bodyLength) + right + '\n';
      text = header;

      for (const key in data) {
        if (Object.hasOwnProperty.call(data, key)) {
          let value = data[key] ? data[key] : 'null';
          let keyLength = key.length;
          let valueLength = value.length;
          let line = `${key}: ${value}`;
          if (line.length <= bodyLength) {
            line += ' '.repeat(bodyLength - line.length);
            text += `| ${line} |\n`;
          } else {
            let first = line.slice(0, bodyLength - 1);
            let last = line.slice(bodyLength, 259);

            text += `| ${first} |\n`;
            text += '| ' + ' '.repeat(keyLength + 2);
            text += '| ' + " ".repeat(bodyLength - last.length) + ' |' + '\n'
          }
        }//end if
      }//end for
      text += header;
      console.log(text);
    },
    /**
     * Se encarga de crear una instancia de transaccion para que puedan ser consumidas
     * por los demas componentes.
     * @param {*} data Datos de la transacción procedente del servidor
     * @returns Instancia de transacción
     */
    processTransactionData(data){
      return {
        id: data.id,
        date: dayjs(data.date),
        description: data.description,
        type: data.type,
        amount: data.amount,
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
      }
    }
  }
}