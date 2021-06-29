<div>
  <div class="table-responsive pt-0" style="height: 60vh" >
    <table class="table table-head-fixed table-hover text-nowrap">
      <thead>
        <tr class="text-center">
          <th>Fecha</th>
          <th class="text-left">Descripción</th>
          <th>Importe</th>
          <th>Saldo</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <template x-for="transaction in transactions" x-bind:key="transaction.id">
          <tr>
            <td class="text-center" x-text="transaction.date.format(dateFormat)" x-bind:title="transaction.date.fromNow()"></td>
            <td class="text-left" x-text="transaction.description"></td>
            <td class="text-right" x-text="formatCurrency(transaction.amount, 0)"></td>
            <td class="text-right" x-text="formatCurrency(transaction.balance,0)"></td>
            <td>
              <template x-if="transaction.type !== 'transfer'">
                <a href="javascript:;" class="btn-tools" x-on:click="enableTransactionForm(transaction)">
                  <i class="fas fa-edit"></i>
                </a>
              </template>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</div>