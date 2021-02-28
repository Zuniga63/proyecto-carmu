<div class="container-fluid">
  @if ($boxId)
      
  @else
    <div class="row justify-content-around">
      @include('livewire.cash-control.show-box.dashboard')
    </div>
  @endif
</div>
