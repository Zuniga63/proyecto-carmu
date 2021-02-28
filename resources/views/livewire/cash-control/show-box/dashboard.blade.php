@foreach ($this->boxs as $box)
<div class="col-lg-4">
  <div class="card card-primary">
    <div class="card-header text-center">
      <h5 class="text-bold mb-0">{{$box['name']}}</h5>
      <p class="m-0" style="font-size: 0.8em">({{$box['business']}})</p>
    </div>
    <div class="card-body">
      <div class="row border-bottom">
        <div class="col-5">Responsable:</div>
        <div class="col-7 text-right">{{$box['cashier']}}</div>
      </div>      
      <div class="row">
        <div class="col-3">Base:</div>
        <div class="col-9 text-right">$50.000</div>
      </div>      
      <div class="row">
        <div class="col-3">Ingresos:</div>
        <div class="col-9 text-right">$50.000</div>
      </div>      
      <div class="row">
        <div class="col-3">Egresos:</div>
        <div class="col-9 text-right">$50.000</div>
      </div>            
      <div class="row border-bottom border-top">
        <div class="col-3">Saldo:</div>
        <div class="col-9 text-right">$50.000</div>
      </div>      
    </div><!--/.end body -->
  </div><!--/.end card -->
</div><!--/.end col -->
@endforeach