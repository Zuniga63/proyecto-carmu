<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Tienda Carmú</title>

  <!-- Fonts -->
  {{-- <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"> --}}

  <link rel="stylesheet" href="./css/app.css">

</head>

<body class="antialised font-sans bg-gray-200 min-h-screen">
  @include('home.navbar')

  <div class="container px-2 md:px-4 mx-auto mb-16">
    <h2 class="text-2xl md:text-3xl lg:text-4xl mb-4">Lo mas nuevo</h2>

    <!-- Contenedor de los productos -->
    <div class="grid grid-cols-2 gap-2 px-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4">
      <!-- item -->
      @foreach ($products as $item)
      <div class="border border-gray-400 rounded-lg p-1 lg:p-3 bg-white">
        <figure class="mb-3 relative">
          @if ($item->is_new)
          <div class="absolute left-0 top-0 p-1 mt-1 ml-1 text-xs sm:text-sm md:text-base tracking-wide text-white font-bold uppercase bg-red-600 rounded-md">
            New
          </div>    
          @endif
          <img src="{{url('storage/' . $item->img)}}" alt="{{$item->name}}" class="block rounded-t-lg w-full" />
        </figure>

        <div class="">
          <!-- Contiene  -->
          <div class="">
            <p class="text-sm sm:text-base lg:text-xl text-gray-600 mb-2">
              {{$item->name}}
            </p>
            <p class="text-xl sm:text-xl lg:text-2xl font-bold text-gray-800">
              $ {{number_format($item->price, 0, '.', "'")}}
            </p>
          </div>
        </div>
      </div>
      
      @endforeach
    </div>
  </div>

  <footer class="bg-gray-900 p-2 text-center">
    <span class="text-white text-xs">Tienda Carmú &copy;2020</span>
  </footer>

  <script src="./js/app.js"></script>
</body>

</html>