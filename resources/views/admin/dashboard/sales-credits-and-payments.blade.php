<div class="card" x-data="monthlyReportsModel()" x-init="initChart()">
  {{-- HEADER CON LOS TABS --}}
  <div class="card-header mb-2">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a  href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'sales'}" x-on:click="changeTab('sales')">Ventas</a>
      </li>
      <li class="nav-item">
        <a href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'payments'}" x-on:click="changeTab('payments')">Abonos</a>
      </li>
      <li class="nav-item">
        <a  href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'credits'}" x-on:click="changeTab('credits')">Créditos</a>
      </li>
      <li class="nav-item">
        <a href="javascript:;" class="nav-link" x-bind:class="{'active' : tab === 'mixed'}" x-on:click="changeTab('mixed')">Combinado</a>
      </li>
    </ul>
  </div>
  {{-- TITULO DE LA GRAFICA --}}
  <h3 class="text-center mb-2">Ventas, Creditos y Abonos [ <span x-text="data.year"></span> ]</h3>

  {{-- SELECTORES DEL PERIODO --}}
  <div class="container">
    <div class="row justify-content-center">
      <div class="form-group row col-md-6">
        <label for="" class="col-5 col-form-label">Periodo</label>
        <select name="" id="" class="form-control col-7" x-model="periodName" x-on:change="changePeriodName">
          <option value="monthly">Mensual</option>
          <option value="quarterly">Trimestral</option>
          <option value="biannual">Semestral</option>
          <option value="annual">Anual</option>
          <option value="annualTremestral">Anual-Trimestral</option>
          <option value="annualSemestral">Anual-Semestral</option>
        </select>
      </div>

      {{-- PERIODOS MENSUALES --}}
      <div class="form-group row col-md-6" x-show.transition.in.duration.400ms="periodName === 'monthly'">
        <label for="montlyName" class="col-3 col-form-label">Mes</label>
        
        <select 
          name="montlyName" 
          id="montlyName" 
          class="form-control col-9" 
          x-model.number="month" 
          x-on:change="updateChart"
        >
          <option value="1">Enero</option>
          <option value="2">Febrero</option>
          <option value="3">Marzo</option>
          <option value="4">Abril</option>
          <option value="5">Mayo</option>
          <option value="6">Junio</option>
          <option value="7">Julio</option>
          <option value="8">Agosto</option>
          <option value="9">Septiembre</option>
          <option value="10">Octubre</option>
          <option value="11">Noviembre</option>
          <option value="12">Diciembre</option>
        </select>
      </div>

      {{-- PERIODOS TRIMESTRALES --}}
      <div class="form-group row col-md-6" x-show.transition.in.duration.400ms="periodName === 'quarterly'">
        <label for="" class="col-6 col-form-label">Trimestre</label>
        <select name="" id="" class="form-control col-6" x-model.number="tremester" x-on:change="updateChart">
          <option value="1">Ene - Mar</option>
          <option value="2">Abr - Jun</option>
          <option value="3">Jul - Sep</option>
          <option value="4">Oct - Dic</option>
        </select>
      </div>

      {{-- PERIODOS SEMESTRALES --}}
      <div class="form-group row col-md-6" x-show.transition.in.duration.400ms="periodName === 'biannual'">
        <label for="" class="col-6 col-form-label">Semestre</label>
        <select name="" id="" class="form-control col-6" x-model.number="semester" x-on:change="updateChart">
          <option value="1">Ene - Jun</option>
          <option value="2">Jul - Dic</option>
        </select>
      </div>
      
    </div>

    <div class="row justify-conten-left">
      <div class="form-check ml-4">
        <input class="form-check-input" type="checkbox" id="accumualated" x-model="accumulated" x-on:change="updateChart">
        <label class="form-check-label" for="accumualated">
          Acumulado
        </label>
      </div>
      <div class="form-check ml-4" x-show.transition="tab === 'mixed'">
        <input class="form-check-input" type="checkbox" id="showLastYear" x-model="showLastYear" x-on:change="updateChart">
        <label class="form-check-label" for="showLastYear">
          Mostrar <span x-text="data.year-1"></span>
        </label>
      </div>

    </div>
  </div>

  <div class="card-body p-0" x-show.transition="tab !== 'table'" id="monthlyReportsCanvasContainer">    
    <canvas id="monthlyReports" wire:ignore></canvas>
  </div>

  {{-- <div class="card-body table-responsive p-0" style="height: 60vh" x-show.transition="tab === 'table'">
    <table class="table table-head-fixed table-hover text-nowrap">
      <thead>
        <tr class="text-center">
          <th>Mes</th>
          <th>Ventas</th>
          <th>Abonos</th>
          <th>Creditos</th>
          <th>Balance</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($montlyReports['reports'] as $report)
        <tr>
          <td>{{$report['month']}}</td>
          <td class="text-right">$ {{number_format($report['sales'], 0, ',', '.')}}</td>
          <td class="text-right">$ {{number_format($report['payments'], 0, ',', '.')}}</td>
          <td class="text-right">$ {{number_format($report['credits'], 0, ',', '.')}}</td>
          <td class="text-right {{$report['balance'] >= 0 ? 'text-success' : 'text-danger'}}">$
            {{number_format($report['balance'], 0, ',', '.')}}</td>
        </tr>
        @endforeach
        <tr class="text-bold">
          <td>Total:</td>
          <td>$ {{number_format($montlyReports['totalSales'], 0, ',', '.')}}</td>
          <td>$ {{number_format($montlyReports['totalPayments'], 0, ',', '.')}}</td>
          <td>$ {{number_format($montlyReports['totalCredits'], 0, ',', '.')}}</td>
          <td>$ {{number_format($montlyReports['totalBalance'], 0, ',', '.')}}</td>
        </tr>
      </tbody>
    </table>
  </div> --}}

  

</div>

{{-- @push('scripts')
<script>

  window.salesPaymentsAndCreditsModel = () => {
    return{
      year:@this.year,
      month: @this.month,
      tremester: @this.tremester,
      semester: @this.semester,
      months: @this.months,
      sales: @this.sales,
      mixedData: @this.getMontlyReports(),
      tab: 'mixed',
      periodName: 'monthly',
      periodValue: 9,
      initChart(){
        console.log(this.periodName, this.periodValue, this.sales);
        let ctx = document.getElementById('monthlyReports');
        window.salesPaymentsCreditsChart = new Chart(ctx, {
          type: 'line',
          data: this.getDataset(),
          options:{
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
          },//end options
        })
      },//end INITCHART()
      getDataset(){
        let labels = [];
        let datasets = [];

        switch(this.periodName){
          case 'monthly':
            let monthIndex = this.periodValue -1;
            let lastYearSales = this.sales[0].monthlySales[monthIndex].accumulatedSales;
            let thisYearSales = this.sales[1].monthlySales[monthIndex].accumulatedSales;

            let maxDays = lastYearSales.length >= thisYearSales.length ? lastYearSales.length : thisYearSales.length;
            console.log(maxDays);

            //Se crean las etiquetas
            for (let index = 1; index <= maxDays; index++) {
              labels.push(index);              
            }

            //Ahora se crean los dataset
            datasets.push({
              label: this.sales[0].year,
              backgroundColor: window.chartColors.blue,
              borderColor: window.chartColors.blue,
              borderWidth: 1,
              data: lastYearSales,
              borderDash: [5,5],
              fill: false
            });
            datasets.push({
              label: this.sales[1].year,
              backgroundColor: window.chartColors.blue,
              borderColor: window.chartColors.blue,
              borderWidth: 1,
              data: thisYearSales,
              fill: false
            });
          break; //End of case monthly
        }

        console.log(labels);
        return {
          labels,
          datasets
        };
      }
    }
  }
</script>
@endpush --}}