<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Tienda Carmú</title>

  <!-- Fonts -->
  {{-- <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"> --}}

  <link rel="stylesheet" href="./css/app.css">

  @env('production')
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-16W3ED57KL"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-16W3ED57KL');
  </script>
  @endenv

</head>

<body class="antialised font-sans bg-gray-200 min-h-screen relative">
  @include('home.navbar')

  <div class="container px-2 md:px-4 mx-auto mb-16">
    <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl mb-4">{{$title}}</h2>

    <!-- Contenedor de los productos -->
    @include('home.product-list')
  </div>


  <footer class="bg-gray-900 p-2 text-center">
    <span class="text-white text-xs">Tienda Carmú &copy;2020</span>
  </footer>

  <script src="./js/app.js"></script>
</body>

</html>