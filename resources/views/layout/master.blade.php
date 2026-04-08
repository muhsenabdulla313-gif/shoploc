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

    // ✅ FIX: define globally
    const isLoggedIn = @json(auth()->check());

    // ✅ GLOBAL TOAST (accessible everywhere)
    function showToast(message, type = 'warning') {
      const existing = document.getElementById('customToast');
      if (existing) existing.remove();

      const toast = document.createElement('div');
      toast.id = 'customToast';
      toast.className = `toast ${type}`;
      toast.innerText = message;

      document.body.appendChild(toast);

      setTimeout(() => toast.classList.add('show'), 100);

      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }
  </script>

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

  @stack('styles')
</head>

<body>

@include('header')

@yield('body')

<script>
document.addEventListener('DOMContentLoaded', function () {

  if (isLoggedIn) {

    let localCart = JSON.parse(localStorage.getItem('cart')) || [];

    if (localCart.length > 0) {
      fetch('/cart/sync', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ cart: localCart })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          localStorage.removeItem('cart'); // ✅ clear after sync
        }
      });
    }

  }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

@stack('scripts')

</body>
</html>