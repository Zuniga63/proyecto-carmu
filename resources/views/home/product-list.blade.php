<div 
  class="grid grid-cols-2 gap-2 px-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4" 
  x-data="{showModal:false, path:'', name:'', showImage(path, name){this.path=path; this.showModal=true; this.name=name}}" 
  x-on:keydown.escape="showModal=false"
>
  <!-- item -->
  @foreach ($products as $item)
  <div class="border border-gray-400 rounded-lg p-1 sm:p-2 lg:p-3 bg-white">
    <figure class="mb-3 relative" x-on:click="showImage('{{url('storage/' . $item->img)}}', '{{$item->name}}')">
      @if ($item->is_new)
      <div class="absolute left-0 top-0 p-1 mt-1 ml-1 text-xs sm:text-sm md:text-base tracking-wide text-white font-bold uppercase bg-red-600 rounded-md">
        New
      </div>    
      @endif
      <img src="{{url('storage/' . $item->img)}}" alt="{{$item->name}}" class="block rounded-t-lg w-full" lazy/>
    </figure>

    <div class="">
      <!-- Contiene  -->
      <div class="">
        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-700">
          $ {{number_format($item->price, 0, ',', ".")}}
        </p>
        <p class="text-sm sm:text-base lg:text-xl text-gray-600 mb-2">
          {{$item->name}}
        </p>
      </div>
    </div>
  </div>  
  @endforeach

  <div 
    class="bg-gray-900 bg-opacity-75 min-w-full min-h-screen z-50 absolute inset-0 flex items-center" 
    x-show="showModal" 
    x-transition:enter="duration-200 ease-out" 
    x-transition:enter-start="opacity-0 scale-95" 
    x-transition:enter-end="opacity-100 scale-100" 
    x-transition:leave="duration-100 ease-in" 
    x-transition:leave-start="opacity-100 scale-100" 
    x-transition:leave-end="opacity-0 scale-95" 
    x-on:click.self="showModal=false"
  >
    {{-- Diseño del header --}}
    <div 
      class="flex-grow bg-gray-900 pb-8 max-w-screen-sm mx-auto sm:max-w-screen-md md:max-w-screen-lg lg:bg-transparent lg:w-auto" 
      x-on:click.self="showModal=false"
    >
      <div class="flex items-center justify-between mb-5 pl-2 pr-2 pt-4 lg:hidden">
        <div>
          <img src="{{url('storage/brand/logo.png')}}" alt="Logo de la tienda carmú" class="h-8 w-auto">
        </div>
        {{-- Close button --}}
        <div class="-mr-2">
          <button 
            type="button" 
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" 
            x-on:click="showModal=false"
          >
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <img class="w-full max-w-md mx-auto md:max-w-lg sm:rounded-lg" x-bind:src="path" x-bind:alt="name">
    </div>
  </div>
</div>