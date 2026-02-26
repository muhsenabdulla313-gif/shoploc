<header class="ashion-navbar">
  <div class="ashion-container" id="headerMainRow">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="ashion-logo" id="logoLink">
      <img src="{{ asset('assets/images/logo.webp') }}" alt="Ashion Logo" class="logo-image" />
    </a>

    <!-- Desktop Links -->
    <nav class="ashion-links" id="desktopLinks">
      <a href="{{ url('/') }}" class="navlink">Home</a>
      <a href="{{ url('/womens') }}" class="navlink">Women</a>
      <a href="{{ url('/contact') }}" class="navlink">Contact</a>
    </nav>

    <!-- Desktop Search -->
    <div class="desktop-search-bar" id="desktopSearchWrap">
      <form id="desktopSearchForm" class="search-form-inline" autocomplete="off">
        <input id="desktopSearchInput" type="text" placeholder="Search products..." class="search-input-inline" />
        <button type="submit" class="search-submit-inline" aria-label="Search">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
      </form>
      <div id="desktopDropdown" class="search-suggestions-dropdown" style="display:none;"></div>
    </div>

    <!-- Right side icons -->
    <div class="ashion-right" id="headerRightIcons">

      @if(!request()->is('login') && !request()->is('register') && !request()->is('verify-otp') && !request()->is('verify-login-otp'))
      <div id="authArea">
        @auth
          <div class="dropdown asion-account-dd">
            <button
              class="icon-btn user-dropdown-btn"
              type="button"
              id="userDropdown"
              data-bs-toggle="dropdown"
              aria-expanded="false"
              aria-label="Account"
            >
              <i class="fa-regular fa-user"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end asion-dropdown" aria-labelledby="userDropdown">
              <li>
                <a class="dropdown-item" href="{{ route('user.profile') }}">
                  <i class="fa-solid fa-user"></i>
                  <span>My Profile</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('user.orders') }}">
                  <i class="fa-solid fa-box"></i>
                  <span>My Orders</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('user.delete') }}"
                   onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                  <i class="fa-solid fa-trash"></i>
                  <span>Delete Account</span>
                </a>
              </li>

              <li><hr class="dropdown-divider asion-divider"></li>

              <li>
                <a class="dropdown-item" href="#"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <i class="fa-solid fa-right-from-bracket"></i>
                  <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
              </li>
            </ul>
          </div>
        @else
          <a href="{{ url('/login') }}" class="icon-btn login-link" aria-label="Login">
            <i class="fa-regular fa-user"></i>
          </a>
        @endauth
      </div>
      @endif

      <button class="icon-btn icon-wrapper" id="wishlistBtn" type="button" title="Wishlist"
              onclick="window.location.href='{{ route('wishlist') }}'">
        <i class="fa-regular fa-heart"></i>
        <span class="badge" id="wishlistBadge" style="display:none;"></span>
      </button>

      <button class="icon-btn icon-wrapper" id="cartBtn" type="button" title="Cart"
              onclick="window.location.href='{{ route('cart') }}'">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="badge" id="cartBadge" style="display:none;"></span>
      </button>

      <button class="menu-toggle icon-btn" id="menuToggle" type="button" title="Menu" aria-label="Open menu">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Search -->
  <div class="responsive-search-container" id="mobileSearchWrap">
    <form id="mobileSearchForm" class="responsive-search-form" autocomplete="off">
      <input id="mobileSearchInput" type="text" placeholder="Search products..." class="responsive-search-input" />
      <button type="submit" class="responsive-search-submit" aria-label="Search">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
    </form>
    <div id="mobileDropdown" class="search-suggestions-dropdown mobile" style="display:none;"></div>
  </div>

  <!-- Mobile Menu Overlay -->
  <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <div class="mobile-drawer">
      <div class="mobile-header">
        <a href="{{ url('/') }}" class="ashion-logo mobile-logo">
          <img src="{{ asset('assets/images/logo.webp') }}" alt="Ashion Logo" class="logo-image mobile-logo-image" />
        </a>

        <button class="close-btn" id="menuCloseBtn" type="button" aria-label="Close menu">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Icons inside menu -->
      <div class="mobile-icons">
        @if(!request()->is('login') && !request()->is('register') && !request()->is('verify-otp') && !request()->is('verify-login-otp'))
          @auth
            <div class="dropdown asion-account-dd">
              <button class="icon-btn" id="mobileAuthBtn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account Options">
                <i class="fa-regular fa-user"></i>
              </button>

              <ul class="dropdown-menu dropdown-menu-end asion-dropdown" aria-labelledby="mobileAuthBtn">
                <li>
                  <a class="dropdown-item" href="{{ route('user.profile') }}">
                    <i class="fa-solid fa-user"></i>
                    <span>My Profile</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('user.orders') }}">
                    <i class="fa-solid fa-box"></i>
                    <span>My Orders</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('user.delete') }}"
                     onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                    <i class="fa-solid fa-trash"></i>
                    <span>Delete Account</span>
                  </a>
                </li>

                <li><hr class="dropdown-divider asion-divider"></li>

                <li>
                  <a class="dropdown-item" href="#"
                     onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                  </a>
                  <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </li>
              </ul>
            </div>
          @else
            <button class="icon-btn" id="mobileAuthBtn" title="Login" type="button"
                    onclick="window.location.href='{{ url('/login') }}'">
              <i class="fa-regular fa-user"></i>
            </button>
          @endauth
        @endif

        <button class="icon-btn" id="mobileSearchFocusBtn" type="button" title="Search" aria-label="Search">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>

        <button class="icon-btn" id="mobileWishlistBtn" type="button" title="Wishlist"
                onclick="window.location.href='{{ route('wishlist') }}'">
          <i class="fa-regular fa-heart"></i>
          <span class="badge" id="mobileWishlistBadge" style="display:none;"></span>
        </button>

        <button class="icon-btn icon-wrapper" id="mobileCartBtn" type="button" title="Cart"
                onclick="window.location.href='{{ route('cart') }}'">
          <i class="fa-solid fa-cart-shopping"></i>
          <span class="badge" id="mobileCartBadge" style="display:none;"></span>
        </button>
      </div>

      <nav class="mobile-links">
        <a href="{{ url('/') }}" class="navlink">Home</a>
        <a href="{{ url('/womens') }}" class="navlink">Women's</a>
        <a href="{{ url('/contact') }}" class="navlink">Contact</a>
      </nav>
    </div>
  </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

  const menuToggle = document.getElementById('menuToggle');
  const menuCloseBtn = document.getElementById('menuCloseBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  const mobileSearchBtn = document.getElementById('mobileSearchFocusBtn');
  const mobileSearchContainer = document.getElementById('mobileSearchWrap');

  // Desktop search elements
  const desktopSearchForm = document.getElementById('desktopSearchForm');
  const desktopSearchInput = document.getElementById('desktopSearchInput');
  const desktopDropdown = document.getElementById('desktopDropdown');

  // Mobile search elements
  const mobileSearchForm = document.getElementById('mobileSearchForm');
  const mobileSearchInput = document.getElementById('mobileSearchInput');
  const mobileDropdown = document.getElementById('mobileDropdown');

  // Search suggestion timeout
  let searchTimeout;

  // Function to show search suggestions
  function showSearchSuggestions(query, dropdownElement, isMobile = false) {
    if (query.length < 2) {
      dropdownElement.style.display = 'none';
      return;
    }

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      fetch(`/admin/products/search-suggestions?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
          if (data.length > 0) {
            let html = '<div class="search-suggestions-list">';
            data.forEach(item => {
              if (item.isCategoryMatch) {
                // Handle both popular categories and database categories
                const displayName = item.isPopularCategory ? item.name : `${item.category} Collection`;
                const description = item.isPopularCategory ? `Browse our ${item.category} collection` : `View all ${item.category} products`;
                
                html += `
                  <div class="search-suggestion-item category-match ${item.isPopularCategory ? 'popular-category' : ''}" data-category="${item.category}">
                    <div class="search-suggestion-name">${displayName}</div>
                    <div class="search-suggestion-category">${description}</div>
                  </div>
                `;
              } else {
                // Regular product match
                html += `
                  <div class="search-suggestion-item" data-id="${item.id}">
                    <div class="search-suggestion-name">${item.name}</div>
                    <div class="search-suggestion-category">${item.category}</div>
                  </div>
                `;
              }
            });
            html += '</div>';
            dropdownElement.innerHTML = html;
            dropdownElement.style.display = 'block';

            // Add click event to suggestion items
            dropdownElement.querySelectorAll('.search-suggestion-item').forEach(item => {
              item.addEventListener('click', function() {
                if (this.classList.contains('category-match')) {
                  // Category match - redirect to shop with category filter
                  const category = this.getAttribute('data-category');
                  window.location.href = `/shop?search=${encodeURIComponent(category)}`;
                } else {
                  // Regular product match
                  const productId = this.getAttribute('data-id');
                  window.location.href = `/product/${productId}`;
                }
              });
            });
          } else {
            dropdownElement.style.display = 'none';
          }
        })
        .catch(error => {
          console.error('Search error:', error);
          dropdownElement.style.display = 'none';
        });
    }, 300);
  }

  // Function to hide search suggestions
  function hideSearchSuggestions(dropdownElement) {
    setTimeout(() => {
      dropdownElement.style.display = 'none';
    }, 150);
  }

  // Desktop search functionality
  if (desktopSearchInput && desktopDropdown) {
    desktopSearchInput.addEventListener('input', function() {
      showSearchSuggestions(this.value, desktopDropdown, false);
    });

    desktopSearchInput.addEventListener('blur', function() {
      hideSearchSuggestions(desktopDropdown);
    });

    desktopSearchForm.addEventListener('submit', function(e) {
      e.preventDefault();
      if (desktopSearchInput.value.trim()) {
        window.location.href = `/shop?search=${encodeURIComponent(desktopSearchInput.value.trim())}`;
      }
    });
  }

  // Mobile search functionality
  if (mobileSearchInput && mobileDropdown) {
    mobileSearchInput.addEventListener('input', function() {
      showSearchSuggestions(this.value, mobileDropdown, true);
    });

    mobileSearchInput.addEventListener('blur', function() {
      hideSearchSuggestions(mobileDropdown);
    });

    mobileSearchForm.addEventListener('submit', function(e) {
      e.preventDefault();
      if (mobileSearchInput.value.trim()) {
        window.location.href = `/shop?search=${encodeURIComponent(mobileSearchInput.value.trim())}`;
      }
    });
  }

  function openMenu(){
    if (!mobileMenu) return;
    mobileMenu.classList.add('active');
    mobileMenu.setAttribute('aria-hidden', 'false');
    document.body.classList.add('menu-open');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu(){
    if (!mobileMenu) return;
    mobileMenu.classList.remove('active');
    mobileMenu.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('menu-open');
    document.body.style.overflow = '';
  }

  if (menuToggle) menuToggle.addEventListener('click', function(e){ e.preventDefault(); openMenu(); });
  if (menuCloseBtn) menuCloseBtn.addEventListener('click', function(e){ e.preventDefault(); closeMenu(); });

  if (mobileMenu) {
    mobileMenu.addEventListener('click', function(e){
      if (e.target === mobileMenu) closeMenu();
    });
  }

  // Mobile search toggle inside menu
  if (mobileSearchBtn && mobileSearchContainer) {
    mobileSearchBtn.addEventListener('click', function(e){
      e.preventDefault();
      mobileSearchContainer.classList.toggle('show');
      const input = document.getElementById('mobileSearchInput');
      if (mobileSearchContainer.classList.contains('show') && input) {
        setTimeout(()=>input.focus(), 50);
      }
    });
  }

  // Badge counts
  function updateBadgeCounts() {
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    const wishlistCount = wishlist.length;

    const wishlistBadge = document.getElementById('wishlistBadge');
    if (wishlistBadge) {
      wishlistBadge.textContent = wishlistCount;
      wishlistBadge.style.display = wishlistCount > 0 ? 'flex' : 'none';
    }
    const mobileWishlistBadge = document.getElementById('mobileWishlistBadge');
    if (mobileWishlistBadge) {
      mobileWishlistBadge.textContent = wishlistCount;
      mobileWishlistBadge.style.display = wishlistCount > 0 ? 'flex' : 'none';
    }

    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    let cartCount = 0;
    cart.forEach(item => { cartCount += (item.qty || 1); });

    const cartBadge = document.getElementById('cartBadge');
    if (cartBadge) {
      cartBadge.textContent = cartCount;
      cartBadge.style.display = cartCount > 0 ? 'flex' : 'none';
    }
    const mobileCartBadge = document.getElementById('mobileCartBadge');
    if (mobileCartBadge) {
      mobileCartBadge.textContent = cartCount;
      mobileCartBadge.style.display = cartCount > 0 ? 'flex' : 'none';
    }
  }

  updateBadgeCounts();
  window.addEventListener('wishlistUpdated', updateBadgeCounts);
  window.addEventListener('cartUpdated', updateBadgeCounts);
  window.addEventListener('storage', function(e) {
    if (e.key === 'wishlist' || e.key === 'cart') updateBadgeCounts();
  });

});
</script>
@endpush

