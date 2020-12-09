{{-- Tarjeta con los datos del cliente --}}
<div class="row justify-content-center mb-5">
  <div class="card">
    <div class="card-header">
      <h4 class="text-bold text-center">{{$customer['fullName']}}</h4>
      @if ($customer['nit'])
      <p class="card-subtitle text-muted text-center">
        (nit: <span class="text-bold">{{is_numeric($customer['nit']) ? number_format($customer['nit'], 0, '', '.') : $customer['nit']}}</span>)
      </p>
      @endif
    </div>
    <div class="card-body">
      <p class="card-text text-center text-bold {{$customer['balanceColor']}} mb-0" style="font-size: 2rem">
        $ {{number_format($customer['balance'], 0, '.', ' ')}}
      </p>
      <p class="text-muted text-center text-small mb-0">{{$customer['state']}}</p>
      @if ($customer['lastCredit'])
      <p class="text-muted text-center text-small mb-0">
        Ultimo credito: {{$customer['lastCredit']['description']}} ($ {{number_format($customer['lastCredit']['amount'], 0, '.', ' ')}})
      </p>
      @endif
      @if ($customer['phone'])
      <p class="card-text m-0">Teléfono: <span class="text-bold">{{$customer['phone']}}</span></p>
      @endif
      @if ($customer['email'])
      <p class="card-text m-0">Correo: <span class="text-bold">{{$customer['email']}}</span></p>
      @endif
    </div>
    <div class="card-footer text-center">
      <a href="{{route('admin.carmu_customers', ['id' => $customer['id']])}}" class="btn btn-primary">
        <i class="fas fa-edit"></i>
        Actualizar Datos
      </a>
    </div>
  </div>
</div>

<div class="row">
  <div class="card col-12" x-data="{tab: 'historyAndCredits'}">
    <div class="card-header mb-2">
      <div class="nav nav-tabs card-header-tabs">
        <li class="nav-item">
          <a href="#historial" class="nav-link px-1 px-sm-4" x-bind:class="{'active' : tab === 'historyAndCredits'}"
            x-on:click="tab = 'historyAndCredits'">Historial</a>
        </li>
        <li class="nav-item">
          <a href="#estadisticas" class="nav-link px-1 px-sm-4" x-bind:class="{'active' : tab === 'statistics'}"
            x-on:click="tab = 'statistics'">Estadisticas</a>
        </li>
        <li class="nav-item">
          <a href="#estadisticas" class="nav-link px-1 px-sm-4" x-bind:class="{'active' : tab === 'transactions'}"
            x-on:click="tab = 'transactions'">Transacciones</a>
        </li>
      </div>
      {{-- /. nav --}}
    </div>
    {{-- ./ card-header --}}

    {{-- Tablas --}}
    <div class="card-body px-0 px-sm-4" x-show.transition="tab === 'historyAndCredits'">
      <div class="row">
        <div class="col-lg-5">
          @include('admin.carmu.customer-profile.history-table')
        </div>

        <div class="col-lg-7">
          @include('admin.carmu.customer-profile.credit-table')
        </div>
      </div>
    </div>
    {{-- /. tablas de historial y creditos --}}

    {{-- ESTADISTICAS --}}
    <div class="card-body px-0 px-sm-4" x-show.transition="tab === 'statistics'" wire:ignore>

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

    </div>

    {{-- FORMULARIO DE TRANSACCIONES --}}
    <div class="card-body px-0 px-sm-4" x-show.transition="tab === 'transactions'">
      <div class="row">
        <div class="col-lg-6">
          <div class="card card-primary" x-data="formData()">
            {{-- HEADER --}}
            <div class="card-header">
              <h3 class="card-title">Registrar Transacción</h3>
            </div>

            {{-- BODY --}}
            <form class="form" x-on:submit.prevent="$wire.store()">
              <div class="card-body">
                {{-- TIPO DE TRANSACCION --}}
                <div class="form-group">
                  <label for="transactionType">Tipo de transacción</label>
                  <select 
                    name="transactionType" 
                    id="transactionType" 
                    class="form-control {{$errors->has('transactionType') ? 'is-invalid' : ''}}" 
                    x-model="type"
                  >
                    <option value="credit">Credito</option>
                    <option value="payment">Abono</option>
                  </select>

                  @error('transactionType')
                  <div class="invalid-feedback" role="alert">
                    {{$message}}
                  </div>
                  @enderror
                </div>


                {{-- FORMA DE PAGO --}}
                <div class="form-group" x-show.transition="type === 'payment'">
                  <label for="paymentType">Forma de pago</label>
                  <select 
                    name="paymentType" 
                    id="paymentType" 
                    class="form-control {{$errors->has('paymentType') ? 'is-invalid' : ''}}" 
                    x-model="paymentType"
                  >
                    <option value="cash" selected>Efectivo</option>
                    <option value="transfer">Transferecia</option>
                  </select>

                  @error('paymentType')
                  <div class="invalid-feedback" role="alert">
                    {{$message}}
                  </div>
                  @enderror
                </div>

                {{-- MOMENTO DE LA TRANSACCIÓN --}}
                <div class="form-group">
                  <label for="transactionDate" wire:ignore>Fecha del <span x-text="type === 'credit' ? 'crédito' : 'abono'"></span></label>
                  <select name="transactionDate" id="transactionDate" class="form-control" x-model="moment">
                    <option value="now">En este momento</option>
                    <option value="other">En otra fecha</option>
                  </select>

                  {{-- LA FECHA A ELEGIR --}}
                  <div class="input-group mt-3" x-show.transition="moment === 'other'">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input 
                      type="date" 
                      name="transactionDate" 
                      class="form-control {{$errors->has('transactionDate') ? 'is-invalid' : ''}}" 
                      x-model="date" 
                      min="{{$this->minDate}}" 
                      max="{{$this->maxDate}}"
                    >

                    @error('transactionDate')
                    <div class="invalid-feedback" role="alert">
                      {{$message}}
                    </div>
                    @enderror
                  </div>
                </div>

                {{-- DESCRIPCION DE LA TRANSACCIÓN --}}
                <div class="form-group" x-show.transition="type === 'credit'">
                  <label for="description" class="required">Descripción</label>
                  <textarea 
                    name="description" 
                    id="description" 
                    rows="3" 
                    class="form-control {{$errors->has('description') ? 'is-invalid' : ''}}" 
                    placeholder="Crea un registro por cada articulo" 
                    x-model="description"
                  ></textarea>
                  @error('description')
                  <div class="invalid-feedback" role="alert">
                    {{$message}}
                  </div>
                  @enderror
                </div>

                {{-- IMPORTE DE LA TRANSACIÓN --}}
                <div class="form-group">
                  <label for="transactionAmount" wire:ignore class="required">Importe del <span x-text="type === 'credit' ? 'crédito' : 'abono'"></span></label>
                  <input 
                    type="text" 
                    name="transactionAmount" 
                    id="transactionAmount" 
                    class="form-control text-right {{$errors->has('transactionAmount') ? 'is-invalid' : ''}}" 
                    placeholder="$ 0.00" 
                    x-on:input="formatInput($event.target)" 
                    x-on:change="$wire.transactionAmount = deleteCurrencyFormat($event.target.value)"
                  >
                  @error('transactionAmount')
                  <div class="invalid-feedback" role="alert">
                    {{$message}}
                  </div>
                  @enderror
                </div>

              </div>

              {{-- FOOTER --}}
              <div class="card-footer">
                <button class="btn btn-primary" type="submit">Registrar</button>
                <button class="btn btn-link">Cancelar transacción</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- Estadisticas --}}


@push('scripts')
<script src="{{asset('assets/pages/js/admin/old-system/main.js') . '?v=1.0'}}"></script>
{{-- <script src="{{asset('assets/pages/js/admin/old-system/main.js') . uniqid('?v=')}}"></script> --}}
<script>
  let data1 = @json($customer['paymentStatistics']);
  let data2 = @json($customer['paymentStatisticsByTimeOfLive']);
  document.addEventListener('livewire:load', ()=>{

    // window.customer = @this.customer;
    window.formData = ()=>{
      return  {
        type:@entangle('transactionType'), 
        moment:@entangle('transactionMoment'), 
        date:@entangle('transactionDate'), 
        paymentType:@entangle('paymentType'), 
        description:@entangle('description'), 
        amount:''
      }
    }

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