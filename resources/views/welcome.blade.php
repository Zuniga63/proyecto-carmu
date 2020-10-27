<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Tienda Carmú</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="./css/app.css">

</head>

<body>
  <!--div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
    @if (Route::has('login'))
    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
      @auth
      <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline">Dashboard</a>
      @else
      <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Login</a>

      {{-- @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
      @endif --}}
      @endif
    </div>
    @endif
  </div> -->
  <div>
    <nav class="bg-gray-800" x-data="{open:false}" x-on:keydown.window.escape="open=false">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <img class="h-8 w-auto" src="{{url('storage/brand/logo.png')}}" alt="Workflow logo">
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <a href="#"
                  class="px-3 py-2 rounded-md text-sm font-medium text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700">Dashboard</a>

                <a href="#"
                  class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Team</a>

                <a href="#"
                  class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Projects</a>

                <a href="#"
                  class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Calendar</a>

                <a href="#"
                  class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Reports</a>
              </div>
            </div>
          </div>
          <div class="hidden md:block">
            <div class="ml-4 flex items-center md:ml-6">
              <!-- Notificación -->
              <button
                class="p-1 border-2 border-transparent text-gray-400 rounded-full hover:text-white focus:outline-none focus:text-white focus:bg-gray-700"
                aria-label="Notifications">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
              </button>

              <!-- Profile dropdown -->
              <div class="ml-3 relative" x-on:click.away="open=false" x-data="{open: false}">
                <div>
                  <button
                    x-on:click="open = !open"
                    class="max-w-xs flex items-center text-sm rounded-full text-white focus:outline-none focus:shadow-solid"
                    id="user-menu" 
                    aria-label="User menu" 
                    aria-haspopup="true"
                  >
                    @auth
                    <img class="h-8 w-8 rounded-full"
                      src="{{Auth::user()->profile_photo_url}}"
                      alt="">
                    @else
                    <!-- Icono de blanck user from heroicon -->
                    <svg class="w-10 h-10 bg-white text-gray-700 rounded-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                  </button>
                </div>
                <!--
                  Profile dropdown panel, show/hide based on dropdown state.
  
                  Entering: "transition ease-out duration-100"
                    From: "transform opacity-0 scale-95"
                    To: "transform opacity-100 scale-100"
                  Leaving: "transition ease-in duration-75"
                    From: "transform opacity-100 scale-100"
                    To: "transform opacity-0 scale-95"
                -->
                <div 
                  class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg"
                  x-show="open"
                  x-transition:enter="transition ease-out duration-100"
                  x-transition:enter-start="transform opacity-0 scale-95"
                  x-transition:enter-end="transform opacity-100 scale-100"
                  x-transition:leave=transition ease-in duration-75"
                  x-transition:leave-start="transform opacity-100 scale-100"
                  x-transition:leave-end="transform opacity-0 scale-95"
                >
                  <div class="py-1 rounded-md bg-white shadow-xs" role="menu" aria-orientation="vertical"
                    aria-labelledby="user-menu">
                    @auth
                    <a href="{{route('profile.show')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Ir al perfil</a>

                    <a href="{{route('admin.admin')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      role="menuitem">Ir al panel</a>

                    <form action="{{route('logout')}}" method="POST">
                      @csrf
                      <button type="submit" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Cerrar sesión</button>
                    </form>
                    @else
                    <a 
                      href="{{route('login')}}"
                      class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                    >
                      Ir al login
                    </a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- Boton del menú --}}
          <div class="-mr-2 flex md:hidden">
            <!-- Mobile menu button -->
            <button
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white" x-on:click="open=!open">
              <!-- Menu open: "hidden", Menu closed: "block" -->
              <svg 
                class="h-6 w-6" 
                x-bind:class="{'block': !open, 'hidden':open}" 
                stroke="currentColor" 
                fill="none" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <!-- Menu open: "block", Menu closed: "hidden" -->
              <svg class="hidden h-6 w-6" x-bind:class="{'block': open, 'hidden':!open}" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!--
        Mobile menu, toggle classes based on menu state.
  
        Open: "block", closed: "hidden"
      -->
      <div class="md:hidden" x-bind:class="{'block':open, 'hidden':!open}">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
          <a href="#"
            class="block px-3 py-2 rounded-md text-base font-medium text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700">Dashboard</a>

          <a href="#"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Team</a>

          <a href="#"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Projects</a>

          <a href="#"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Calendar</a>

          <a href="#"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Reports</a>
        </div>
        <!-- Informacion del usuario -->
        <div class="pt-4 pb-3 border-t border-gray-700">
          <div class="flex items-center px-5 space-x-3">
            <div class="flex-shrink-0">
              {{-- user-circle de heroicon --}}
              @auth
              <div class="flex-shrink-0">
                <img class="h-10 w-10 rounded-full" src="{{Auth::user()->profile_photo_url}}" alt="">
              </div>
              <div class="space-y-1">
                <div class="text-base font-medium leading-none text-white">{{Auth::user()->name}}</div>
                <div class="text-sm font-medium leading-none text-gray-400">{{Auth::user()->email}}</div>
              </div>
              @else
              <svg class="w-10 h-10 bg-white text-gray-700 rounded-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              @endif
            </div>
            <div class="space-y-1">
              @auth

              @else
              <div class="text-base font-medium leading-none text-white">Invitado</div>
              <div class="text-sm font-medium leading-none text-gray-400">Bienvenido a nuestra plataforma</div>
              @endif
            </div>
          </div>
          <div class="mt-3 px-2 space-y-1">
            @auth
            <a href="{{route('profile.show')}}"
              class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Ir al perfil</a>

            <a href="{{route('admin.admin')}}"
              class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Admin</a>

            <form action="{{route('logout')}}" method="POST">
              @csrf
              <button type="submit" href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Cerrar sesión</button>
            </form>
            @else
            <a href="{{route('login')}}"
              class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Ir al login</a>
            @endif
          </div>
        </div><!--./end user info-->


      </div>
    </nav>

    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold leading-tight text-gray-900">
          Dashboard
        </h1>
      </div>
    </header>
    <main>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Replace with your content -->
        <div class="px-4 py-6 sm:px-0">
          <div class="border-4 border-dashed border-gray-200 rounded-lg h-96"></div>
        </div>
        <!-- /End replace -->
      </div>
    </main>
  </div>

  <script src="./js/app.js"></script>
</body>

</html>