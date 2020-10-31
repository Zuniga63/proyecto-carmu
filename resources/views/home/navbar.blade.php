<header class="relative bg-white mb-4" x-data="{toggler:false}">
  <div class="px-4 md:px-6 max-w-6xl mx-auto">
    {{-- Menú general --}}
    <div class="flex justify-between items-center border-b-2 border-gray-100 py-6 lg:justify-start lg:space-x-10">
      {{-- Logo de la empresa --}}
      <div class="lg:w-0 lg:flex-1">
        <a href="{{url('/')}}" class="flex">
          <img src="{{url('storage/brand/logo.png')}}" alt="Logo de Tienda Carmú" class="h-8 w-auto sm:h-10">
        </a>
      </div>

      {{-- Toggler que abre y cierra el menú movil --}}
      <div class="-mr-2 -my-2 lg:hidden">
        <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 focus:text-gray-500" x-on:click="toggler=!toggler">
          <svg
                class="h-6 w-6"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
        </button>

      </div>

      {{-- Menú general --}}
      <nav class="hidden lg:flex space-x-10">
        {{-- Link to home --}}
        <a href="{{url('/')}}" class="flex items-start space-x-1 rounded-lg text-base leading-6 font-medium text-gray-500 hover:text-gray-900 focus:outline-none focus:text-gray-900 transition ease-in-out duration-150">
          <svg class="flex-shrink-0 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            </path>
          </svg>
          <div class="text-base leading-6 font-medium">Home</div>
        </a>

        {{-- Link to products --}}
        <div class="relative" x-data="{active:false}" x-on:click.away="active=false">
          <button x-bind:class="{'text-gray-500': !active, 'text-gray-900': active}"
            class="group inline-flex items-center space-x-2 text-base leading-6 font-medium hover:text-gray-900 focus:outline-none focus:text-gray-900 transition ease-in-out duration-150"
            x-on:click="active = !active">

            <div class="flex items-start space-x-1 rounded-lg text-base leading-6 font-medium group-hover:text-gray-900 group-focus:outline-none group-focus:text-gray-900 transition ease-in-out duration-150">

              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
              </svg>

              <span>Catalogo</span>
            </div>

            <svg x-bind:class="{'text-gray-400': !active, 'text-gray-600': active}"
              class="h-5 w-5 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150"
              xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
          
          </button>

          {{-- Dropdown --}}
          <div class="absolute -ml-4 mt-3 transform px-2 w-screen max-w-md sm:px-0 lg:ml-0 lg:left-1/2 lg:-translate-x-1/2"
            x-show="active" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1">
            <div class="rounded-lg shadow-lg">
              <div class="rounded-lg shadow-xs overflow-hidden">
                <div class="z-20 relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                  <a href="{{route('catalog')}}"
                    class="-m-3 p-3 flex items-start space-x-4 rounded-lg hover:bg-gray-50 transition ease-in-out duration-150">
                    <!-- Heroicon name: clock -->
                    <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="space-y-1">
                      <p class="text-base leading-6 font-medium text-gray-900">
                        Relojería
                      </p>
                      <p class="text-sm leading-5 text-gray-500">
                        De las marcas Q&Q y Q&Q Superior
                      </p>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>

      {{-- Enlace al login o perfil del usuario en la version general --}}
      <div class="hidden lg:flex items-center justify-end space-x-8 md:flex-1 lg:w-0">
        @auth
        <div class="ml-3 relative" x-data="{active:false}" x-on:click.away="active=false">
          <div>
            <button class="max-w-xs flex items-center text-sm rounded-full text-white focus:outline-none focus:shadow-solid"
              id="user-menu" aria-label="User menu" aria-haspopup="true" x-on:click="active=!active">
              <img class="h-8 w-8 rounded-full"
                src="{{Auth::user()->profile_photo_url}}"
                alt="{{Auth::user()->name}}" />
            </button>
          </div>
          
          <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg" x-show="active"
            x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95">
            <div class="py-1 rounded-md bg-white shadow-xs" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
              <a href="{{route('profile.show')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Ir al perfil</a>
        
              <a href="{{route('admin.admin')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Panel de administración</a>
        
              <form action="{{route('logout')}}" method="POST">
                @csrf
                <button type="submit" href="#" class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">Cerrar sesión</button>
              </form>
            </div>
          </div>
        </div>
        @else
        <a href="{{route('login')}}" class="whitespace-no-wrap text-base leading-6 font-medium text-gray-500 hover:text-gray-900 focus:outline-none focus:text-gray-900">
          Iniciar Sesión
        </a>
        @endif
      </div>
    </div>

    {{-- Menú de la version movil --}}
    <div class="absolute top-0 inset-x-0 p-2 transition transform origin-top-right lg:hidden z-50" x-show="toggler" x-transition:enter="duration-200 ease-out" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="duration-100 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
      <div class="rounded-lg shadow-lg">
        <div class="rounded-lg shadow-xs divide-y-2 divide-gray-50 bg-white">
          {{-- Header y links --}}
          <div class="pt-5 pb-6 px-5 space-y-6">
            {{-- Imagen del logo y boton de cierre --}}
            <div class="flex items-center justify-between">
              {{-- Logo --}}
              <div>
                <img src="{{url('storage/brand/logo.png')}}" alt="Logo de la tienda carmú" class="h-8 w-auto">
              </div>
              {{-- Close button --}}
              <div class="-mr-2">
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" x-on:click="toggler=false">
                  <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            {{-- Links de la aplicación --}}
            <nav class="grid gap-y-8">
              <!-- Link to home -->
              <a href="#" class="-m-3 p-3 flex items-center space-x-3 rounded-md hover:bg-gray-50 transition ease-in-out duration-150">
                <!-- Heroicon:home -->
                <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                  </path>
                </svg>
                <div class="text-base leading-6 font-medium text-gray-900">
                  Home
                </div>
              </a>
              <!-- Link to products -->
              <div class="-m-3 p-3 flex items-top space-x-3 rounded-md hover:bg-gray-50 transition ease-in-out duration-150">
                <!-- Heroicon:shop -->
                <svg class="flex-shrink-0 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                  </path>
                </svg>
                <div class="text-base leading-6 font-medium text-gray-900">
                  Catalogo
                  <div class="mt-3 px-2 space-y-1">
                    <a href="{{route('catalog')}}"
                      class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Relojería</a>
                  </div>
                </div>
              </div>
            </nav>
          </div>

          {{-- Links de control de usuario --}}
          <div class="py-6 px-5 space-y-6">
            @auth
            <div class="pt-4 pb-3">
              {{-- Información del usuario --}}
              <div class="flex items-center px-5 space-x-3">
                <div class="flex-shrink-0">
                  <img src="{{Auth::user()->profile_photo_url}}" alt="" class="h-10 w-10 rounded-full">
                </div>

                <div class="space-y-1">
                  <div class="text-base font-medium leading-none text-gray-800">
                    {{Auth::user()->name}}
                  </div>
                  <div class="text-sm font-medium leading-none text-gray-500">
                    {{Auth::user()->email}}
                  </div>

                </div>
              </div>

              {{-- Links importantes --}}
              <div class="mt-3 px-2 space-y-1">
                <a href="{{route('profile.show')}}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">
                  Ir al perfil
                </a>
                <a href="{{route('admin.admin')}}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">
                  Panel de administración
                </a>
                <form action="{{route('logout')}}" method="POST">
                  @csrf
                  <button type="submit" href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Cerrar sesión</button>
                </form>
              </div>
            </div>
            @else
            <div class="space-y-6">
              <p class="text-center text-base leading-6 font-medium text-gray-500">
                <a href="{{route('login')}}" class="text-indigo-600 hover:text-indigo-500 transition ease-in-out duration-150">
                  Iniciar Sesión
                </a>
              </p>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    {{-- Fin del menú movil --}}
  </div>

</header>
