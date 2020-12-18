<div 
  x-data="{
    graphPeriod: @entangle('graphPeriod'),
    graphCategory: @entangle('graphCategory'),
  }"
>
  <div class="row justify-content-around mb-2">
    <div class="form-group row col-md-5 col-lg-4 mb-0">
      <label for="" class="col-4 col-form-label">Periodo</label>
      <select name="" id="" class="form-control col-8" x-model="graphPeriod" {{-- x-on:change="setPeriod"  --}}
        {{-- x-on:input="specificPeriod = 1" --}}>
        @foreach ($graphPeriods as $key => $period)
        <option value="{{$key}}">{{$period}}</option>
        @endforeach
      </select>
    </div>

    <div class="form-group row col-md-5 col-lg-4 mb-0">
      <label for="" class="col-4 col-form-label">Categoría</label>
      <select name="" id="" class="form-control col-8" x-model="graphCategory">
        <option value="all">Todas</option>
        @foreach ($this->categories as $key => $categoryName)
        <option value="{{$key}}">{{$categoryName}}</option>
        @endforeach
      </select>
    </div>

    <button class="btn btn-success col-lg-2" x-on:click="updateGraph(await $wire.graphData())">Generar</button>
  </div>

  <div id="graphContainer" wire:ignore style="max-width: 1200px">
    <canvas id="salesChart"></canvas>
  </div>
</div>