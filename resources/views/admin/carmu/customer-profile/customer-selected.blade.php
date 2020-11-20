{{-- Tarjeta con los datos del cliente --}}
<div class="row justify-content-center mb-5">
  <div class="card">
    <div class="card-header">
      <h4 class="text-bold text-center">{{$this->fullName}}</h4>
      @if ($customer->nit)
      <p class="card-subtitle text-muted text-center">
        (nit: <span class="text-bold">{{number_format($customer->nit, 0, '', '.')}}</span>)
      </p>
      @endif
    </div>
    <div class="card-body">
      <p class="card-text text-center text-bold {{$this->state}} mb-0" style="font-size: 2rem">
        $ {{number_format($this->balance, 0, '.', ' ')}}
      </p>
      <p class="text-muted text-center text-small mb-0">{{$this->dateDiff}}</p>
      @if ($lastCredit)
      <p class="text-muted text-center text-small mb-0">
        Ultimo credito: {{$lastCredit->description}} ($ {{number_format($lastCredit->amount, 0, '.', ' ')}})
      </p>
      @endif
      @if ($customer->phone)
      <p class="card-text m-0">Teléfono: <span class="text-bold">{{$customer->phone}}</span></p>
      @endif
      @if ($customer->email)
      <p class="card-text m-0">Correo: <span class="text-bold">{{$customer->email}}</span></p>
      @endif
    </div>
    <div class="card-footer text-center">
      <a href="{{route('admin.carmu_customers', ['id' => $customer->customer_id])}}" class="btn btn-primary">
        <i class="fas fa-edit"></i>
        Actualizar Datos
      </a>
    </div>
  </div>
</div>

{{-- Tablas --}}
<div class="row">
  <div class="col-lg-5">
    @include('admin.carmu.customer-profile.history-table')
  </div>

  <div class="col-lg-7">
    @include('admin.carmu.customer-profile.credit-table')
  </div>
</div>

{{-- Estadisticas --}}
<div class="row">
  <div class="col-md-6">
    <div class="card card-dark">
      <div class="card-header">
        Creditos segun su vencimiento
      </div>
      <div class="card-body">
        <canvas id="paymentStatistics"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card card-dark">
      <div class="card-header">
        Creditos segun el tiempo de vida
      </div>
      <div class="card-body">
        <canvas id="statisticsByTiemOfLive"></canvas>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  let data1 = @json($this->paymentStatistics);
  let data2 = @json($this->paymentStatisticsByTimeOfLive);
  document.addEventListener('livewire:load', ()=>{
    let ctx1 = document.getElementById('paymentStatistics');
    let ctx2 = document.getElementById('statisticsByTiemOfLive')
    let paymentStatistics = new Chart(ctx1, {
      type:'doughnut',
      data:{
        labels:['A tiempo', 'Vencidos'],
        datasets:[{
          data:[data1.success.count, data1.expired.count],
          label: 'Conteo',
          backgroundColor:[
            'rgba(75, 192, 192, 0.2)',
            'rgba(255, 99, 132, 0.2)',
          ],
          borderColor:[
            'rgba(75, 192, 192, 1)',
            'rgba(255, 99, 132, 1)',
          ],
          borderWidth: 1
        },
        {
          data:[data1.success.weighted, data1.expired.weighted],
          label:'Peso',
          backgroundColor:[
            'rgba(75, 192, 192, 0.2)',
            'rgba(255, 99, 132, 0.2)',
          ],
          borderColor:[
            'rgba(75, 192, 192, 1)',
            'rgba(255, 99, 132, 1)',
          ],
          borderWidth: 1,
        }]
      },
      options:{
        responsive:true,
        circumference: Math.PI,
        rotation: -Math.PI,
        legend:{
          position:'top',
        },
        title:{
          display:false,
          text: 'Créditos segun su vencimiento'
        },
        tooltips:{
            callbacks:{
              label: function (tooltipItem, data){
                let dataset = data.datasets[tooltipItem.datasetIndex];
                let label = dataset.label || '';
                let currentValue = dataset.data[tooltipItem.index]

                if (label) {
                  label += ': ';
                }
                
                if(tooltipItem.datasetIndex == 0){
                  let count = dataset.data.reduce((accumulator, currentValue) => accumulator + currentValue)
                  label += currentValue + ` de ${count}`;
                }else{
                  console.log(currentValue);
                  label += Math.round(currentValue * 100);
                }
                return label;
              }
            }
          }
      }
    })

    let statisticsByTiemOfLive = new Chart(ctx2, {
      type:'doughnut',
      data:{
        labels:['1 Mes', '2 Meses', '3 Meses', 'Mas de 3 meses'],
        datasets:[{
          label: 'Conteo',
          data:[data2.oneMonth.count, data2.twoMonths.count, data2.threeMonths.count, data2.moreThanThreeMonts.count],
          backgroundColor:[
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.4)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
          ],
          borderColor:[
            'rgba(75, 192, 192, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(255, 99, 132, 1)',
          ],
          borderWidth: 1
        },
        {
          data:[data2.oneMonth.weighted, data2.twoMonths.weighted, data2.threeMonths.weighted, data2.moreThanThreeMonts.weighted],
          label:'Peso',
          backgroundColor:[
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.4)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
          ],
          borderColor:[
            'rgba(75, 192, 192, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(255, 99, 132, 1)',
          ],
          borderWidth: 1
        }
      ]
      },
      options:{
        responsive:true,
        legend:{
          position:'bottom',
        },
        circumference: Math.PI,
        rotation: -Math.PI,
        title:{
          display:false,
          text: 'Créditos segun su vencimiento'
        },
        tooltips:{
            callbacks:{
              label: function (tooltipItem, data){
                let dataset = data.datasets[tooltipItem.datasetIndex];
                let label = dataset.label || '';
                let currentValue = dataset.data[tooltipItem.index]

                if (label) {
                  label += ': ';
                }
                
                if(tooltipItem.datasetIndex == 0){
                  let count = dataset.data.reduce((accumulator, currentValue) => accumulator + currentValue)
                  label += currentValue + ` de ${count}`;
                }else{
                  console.log(currentValue);
                  label += currentValue;
                }
                return label;
              }
            }
          }

      }
    })

  })
</script>
@endpush