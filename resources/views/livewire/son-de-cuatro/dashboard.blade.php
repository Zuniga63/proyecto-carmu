<x-slot name="header">
  <h2 class="font-semibold text-xl text-gray-800 leading-tight" wire:click="render">
    {{ __('Productos Son De Cuatro') }}
  </h2>
</x-slot>

<main class="pt-5">
  @foreach ($products as $item)
  <div class="border-2 border-gray-600 rounded-md | mx-2 mb-4 | p-2 | divide-y divide-gray-400 | bg-white | scrollReveal" x-data>
    <h3 class="text-center text-lg font-bold text-gray-700">{{$item['name']}}</h3>
    <div class="grid grid-cols-3 py-2">
      <div class="flex flex-col">
        <p class="text-center">Costo</p>
        <p class="text-center text-sm" x-text="formatCurrency({{ $item['expense'] }}, 0)"></p>
      </div>
      <div class="flex flex-col">
        <p class="text-center">Precio</p>
        <p class="text-center text-sm" x-text="formatCurrency({{ $item['price'] }} , 0)"></p>
      </div>
      <div class="flex flex-col">
        <p class="text-center">Utilidad</p>
        <p class="text-center italic text-sm">{{ $item['utility'] }}%</p>
      </div>
    </div>

    <figure class="py-2">
      <img src="{{ $item['img'] }}" alt="" class="border-4 mx-auto w-auto"
        wire:click="render" loading="lazy">
    </figure>
  </div>
  @endforeach

</main>

@push('styles')
    {{-- Librería de scroll reveal --}}
  <script src="https://unpkg.com/scrollreveal"></script>
@endpush

@push('scripts')
<script>
  ScrollReveal().reveal('.scrollReveal');

  window.formatCurrency = (number, fractionDigits) => {
    var formatted = new Intl.NumberFormat('es-CO', {
      style: "currency",
      currency: 'COP',
      minimumFractionDigits: fractionDigits,
    }).format(number);
    return formatted;
  }

</script>
@endpush