<?php

namespace App\Http\Livewire\CashControl;

use App\Models\CashControl\Box;
use Carbon\Carbon;
use Error;
use Livewire\Component;

use function PHPUnit\Framework\throwException;

class ShowBoxs extends Component
{
  public ?int $boxId = null;


  public function getBoxsProperty()
  {
    $boxs = [];
    if (empty($this->boxId)) {
      $boxData = Box::orderBy('id')->with(['business', 'cashier'])->get();

      foreach ($boxData as $data) {
        $boxs [] = $this->getBoxInfo($data);
      }
    }

    return $boxs;
  }

  protected function getBoxInfo(Box $box)
  {
    $transactionTypes = [
      'general'   => ['income' => 0, 'expense' => 0],
      'sale'      => ['income' => 0, 'expense' => 0],
      'expense'   => ['income' => 0, 'expense' => 0],
      'purchase'  => ['income' => 0, 'expense' => 0],
      'service'   => ['income' => 0, 'expense' => 0],
      'credit'    => ['income' => 0, 'expense' => 0],
      'payment'   => ['income' => 0, 'expense' => 0]
    ];

    $business     = $box->business ? $box->business->name : 'Negocio no asignado';
    $cashier      = $box->cashier ? $box->cashier->name : 'Cajero no asignado';
    $closingDate  = $box->closing_date;
    $base         = round($box->base);
    $sales = $services = $payments = $otherIncomes = $incomesAmount = 0;
    $expenses = $purchase = $credits = $otherExpenses = $expensesAmount = 0;
    $calBalance = $base;
    $transactionsCount = 0;

    //Se consulta el numero de transacciones para limintar las consultas
    $transactionsCount = $box->transactions()->where('transaction_date', '>=', $closingDate)->count();

    //Se consultan los ingresos por ventas
    foreach ($transactionTypes as $type => $result) {
      if($transactionsCount > 0){
        $result['income'] = $this->getBoxTypeAmount($type, $box, $transactionsCount);
        $result['expense'] = $this->getBoxTypeAmount($type, $box, $transactionsCount, false);
      }
      $transactionTypes[$type] = $result;
    }

    $sales = $transactionTypes['sale']['income'];
    $services = $transactionTypes['service']['income'];
    $payments = $transactionTypes['payment']['income'];
    $otherIncomes = $transactionTypes['general']['income'];
    $incomesAmount = $sales + $services + $payments + $otherIncomes;

    $expenses =$transactionTypes['expense']['expense'];
    $purchase =$transactionTypes['purchase']['expense'];
    $credits=$transactionTypes['credit']['expense'];
    $otherExpenses=$transactionTypes['general']['expense'];
    $expensesAmount=$expenses + $purchase + $credits + $otherExpenses;

    $calBalance += $incomesAmount + $expensesAmount;
    $closingDate = Carbon::createFromFormat('Y-m-d H:i:s', $closingDate)
      ->isoFormat('MMMM Do YYYY, h:mm:ss a');

    return [
      'id'              => $box->id,
      'name'            => $box->name,
      'closeDate'       => $closingDate,
      'base'            => $base,
      'business'        => $business,
      'cashier'         => $cashier,
      'sales'           => $sales,
      'services'        => $services,
      'payments'        => $payments,
      'otherIncomes'    => $otherIncomes,
      'incomesAmount'   => $incomesAmount,
      'expenses'        => $expenses,
      'purchases'       => $purchase,
      'credits'         => $credits,
      'otherExpenses'   => $otherExpenses,
      'expensesAmount'  => $expensesAmount,
      'balance'         => $calBalance,
    ];    
  }

  /**
   * Se encarga de recupear el importe de las transacciones que cumple con el tipo
   * @param string $transactionType Tipo de transacción de las siete posible
   * @param Box $box Instancia del modelo de caja
   * @param int $globalCount Es el contador global de transacciones
   * @param bool $income True para recuperar ingresos, false para recuperar egresos
   * @return float Suma de todas las transacciones que cumplen las condicones
   */
  protected function getBoxTypeAmount(string $transactionType, Box $box, int &$globalCount, bool $income = true)
  {
    $localCount = 0;
    $closingDate = $box->closing_date;
    $amount = 0;

    /** Se construye la consulta */
    $query = $box->transactions()->where('transaction_date', '>=', $closingDate);

    /**
     * Se definesi se van a recuperar solo los
     * ingresos o los egresos
     */
    if ($income) {
      $query->where('amount', '>', 0);
    } else {
      $query->where('amount', '<', 0);
    }

    switch ($transactionType) {
      case 'general':
        $query->where('type', 'general');
        break;
      case 'sale':
        $query->where('type', 'sale');
        break;
      case 'expense':
        $query->where('type', 'expense');
        break;
      case 'purchase':
        $query->where('type', 'purchase');
        break;
      case 'service':
        $query->where('type', 'service');
        break;
      case 'credit':
        $query->where('type', 'credit');
        break;
      case 'payment':
        $query->where('type', 'payment');
        break;
      default:
        throw new Error("Tipo de transaccion no soportado");
        break;
    }

    $localCount = $query->count();
    if ($localCount > 0) {
      $amount = round($query->sum('amount'));
      $globalCount -= $localCount;
    }

    return $amount;
  }

  public function render()
  {
    return view('livewire.cash-control.show-boxs')->layout('livewire.cash-control.show-box.index');
  }

  public function mount($id = null)
  {
    //TODO
  }
}
