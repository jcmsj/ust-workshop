<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @inject('metadata', 'App\Services\Metadata')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- <title>{{ $metadata::get('title') ?? config('app.name', 'Laravel') }}</title>
  @foreach($metadata::getMetaTags() as $name => $content)
  <meta name="{{ $name }}" content="{{ $content }}">
  @endforeach
  @foreach($metadata::getOpenGraphMetaTags() as $property => $content)
  <meta property="{{ $property }}" content="{{ $content }}">
  @endforeach
  <link rel="canonical" href="{{ url()->current() }}"> --}}

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Google tag (gtag.js) -->
  {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-69MC228RDR"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

      function gtag() {
          dataLayer.push(arguments);
      }
      gtag('js', new Date());

      gtag('config', 'G-69MC228RDR');
  </script> --}}

  <!-- Styles -->
  @livewireStyles
  @filamentStyles
</head>

<body data-theme="hipandvalley">
  <div class="drawer">
    <input id="app-drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content flex flex-col">
      <!-- Navbar -->
      <div class="navbar xl:bg-base-100 xl:z-50 sticky top-0">
        <div class="flex-none xl:hidden">
          <label for="app-drawer" aria-label="open sidebar" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              class="inline-block h-5 w-5 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </label>
        </div>
        <div class="flex-1 hidden xl:flex items-center">
          <a class="btn btn-ghost text-xl" href="{{ route('home') }}">
            <img src="/LEADS.webp" alt="Hip and valley financial solutions full logo" class="h-14" />
          </a>
        </div>
      </div>
      <!-- Page content here -->
      <div class="min-h-screen font-sans text-gray-900 dark:text-gray-100 antialiased p-2">
        <main>
          {{ $slot }}
        </main>
      </div>
    </div>
    <div class="drawer-side">
      <label for="app-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
      <ul class="menu menu-lg bg-base-200 min-h-full w-80 p-4 ">
        <!-- Sidebar content here -->
        <li><a href="{{ route('home') }}"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
              viewBox="0 0 24 24">
              <path fill="currentColor"
                d="M4.5 21q-.625 0-1.062-.437T3 19.5v-1.9l4-3.55V21zM8 21v-4h8v4zm9 0v-8.2L12.725 9l3.025-2.675l4.75 4.225q.25.225.375.513t.125.612V19.5q0 .625-.437 1.063T19.5 21zM3 16.25v-4.575q0-.325.125-.625t.375-.5L11 3.9q.2-.2.463-.287T12 3.525t.538.088T13 3.9l2 1.775z" />
            </svg> Home</a></li>

        @if(isset($sidebar))
        {{ $sidebar }}
        @endif

        <!-- Contact Information -->
        {{-- <div class="divider">Contact Us</div> --}}
        {{-- <li>
          <a href="tel:2043205201" class="flex items-center gap-2">
            <ms-phone-android-outline />
            <span>(204) 320-5201</span>
          </a>
        </li> --}}
        <li>
    
        </li>
      </ul>
    </div>
  </div>

  @stack('modals')

  @livewireScripts
  @filamentScripts
  @livewire('notifications')
</body>

</html>
