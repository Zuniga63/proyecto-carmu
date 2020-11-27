<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Tienda Carmú</title>

  <!-- Fonts -->
  {{-- <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"> --}}

  <link rel="stylesheet" href="{{asset('/css/app.css')}}">
  <style>
    :target::before {
      content: "";
      display: block;
      height: 3.75rem; /* aquí la altura de la cabecera fija*/
      margin: -3.75rem 0 0; /* altura negativa de la cabecera fija */
    }
  </style>

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

  {{-- Librería de scroll reveal --}}
  <script src="https://unpkg.com/scrollreveal"></script>

</head>

<body class="antialised font-sans bg-gray-200 min-h-screen relative pt-px">
  @include('home.navbar')

  <div class="container px-2 md:px-4 mx-auto mb-16 mt-16 lg:mt-20">
    <h2 class="text-lg md:text-2xl lg:text-3xl mb-4 text-indigo-600 font-bold">{{$title}}</h2>

    <!-- Contenedor de los productos -->
    @include('home.product-list')

    @if($home)
    <div class="grid grid-cols-2 gap-4 mb-5">
      <div class="col-span-full lg:col-span-1 scrollReveal">
        <a href="#dondeEcontrarnos" class="text-xl md:text-2xl lg:text-3xl mb-4 text-indigo-600 font-bold block" id="dondeEcontrarnos"># ¿Donde Encontrarnos?</a>
        @include('home.location')
      </div>

      <div class="col-span-full lg:col-span-1 scrollReveal">
        <a href="#dondeEcontrarnos" class="text-xl md:text-2xl lg:text-3xl mb-4 text-indigo-600 font-bold block" id="galeria"># Galería</a>
        @include('home.galery')
      </div>
    </div>
    @endif
  </div>


  <footer class="bg-gray-900 p-2 text-center scrollReveal">
    <span class="text-white text-xs">Tienda Carmú &copy;2020</span>
  </footer>

  <script src="{{asset('js/app.js')}}"></script>
  <script>
    const links = document.querySelectorAll('a[href^="#"]');
    for(const link of links){
      link.addEventListener('click', clickHandler);
    }

    ScrollReveal().reveal('.scrollReveal');

    function clickHandler(e){
      e.preventDefault();
      const href = this.getAttribute("href");
      location.hash = href;
      // const offsetTop = document.querySelector(href).offsetTop;

      // scrollTo({
      //   top: offsetTop,
      //   behavior: "smooth"
      // })
      document.querySelector(href).scrollIntoView({
        behavior: "smooth"
      })
    }

    window.clickHandler = clickHandler;
  </script>
</body>

</html>