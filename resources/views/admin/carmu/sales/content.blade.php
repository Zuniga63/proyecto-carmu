<div class="card" 
  x-data="{
    tab:'table',
  }"
>
  <div class="card-header mb-2">
    <div class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a href="javascript:;" class="nav-link px-1 px-sm-4" x-bind:class="{'active' : tab === 'table'}"
          x-on:click="tab = 'table'" disabled>Datos</a>
      </li>
      <li class="nav-item">
        <a href="javascript:;" class="nav-link px-1 px-sm-4" x-bind:class="{'active' : tab === 'graph'}"
          x-on:click="tab = 'graph'">Gráficas</a>
      </li>
      {{-- <li class="nav-item">
        <a href="javascript:;" class="nav-link px-1 px-sm-4" x-bind:class="{'active' : tab === 'invoicing'}"
          x-on:click="tab = 'invoicing'">Facturación</a>
      </li> --}}
    </div>
    {{-- /. nav --}}
  </div>
  {{-- ./ card-header --}}
  <div class="card-body">
    <div x-show.transition.in="tab === 'table'">
      @include('admin.carmu.sales.table')
    </div>

    <div x-show.transition.in="tab === 'graph'">
      @include('admin.carmu.sales.graphs')
    </div>

    {{-- <div x-show.transition.in="tab === 'invoicing'">
      @include('admin.carmu.sales.invoice')
    </div> --}}
  </div>
