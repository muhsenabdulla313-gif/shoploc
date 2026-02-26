<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title','Website')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @auth
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
  @endauth

  <script>
    window.Laravel = { csrfToken: '{{ csrf_token() }}' };
  </script>

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

  {{-- Optional: page-level styles --}}
  @stack('styles')
</head>

<body>

  @include('header')

  @yield('body')

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

  {{-- ✅ Page scripts should be pushed here --}}
  @stack('scripts')

</body>
</html>
