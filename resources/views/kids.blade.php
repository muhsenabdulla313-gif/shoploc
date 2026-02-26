@extends('layout.master')

@section('body')

<div class="kids-page">

  <!-- HERO -->
  <section class="kids-hero">
    <div class="hero-overlay">
      <h1>Kids Fashion Collection</h1>
      <p>Bright, colorful, and comfortable clothing for your little ones</p>

      <div class="hero-buttons">
        <button class="hero-btn" id="shopNowBtn">Shop Now</button>
        <button class="hero-btn secondary" id="newArrivalsBtn">New Arrivals</button>
      </div>
    </div>

    <!-- Bottom curve -->
    <div class="hero-curve hero-curve-bottom invert" aria-hidden="true">
      <svg class="curve-svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path class="curve-path"
          d="M0,0V40C100,80,200,0,300,20C400,40,500,80,600,60C700,40,800,0,900,20C1000,40,1100,80,1200,40V0H0Z" />
      </svg>
    </div>
  </section>

  <!-- CONTAINER -->
  <div class="kids-container">

    <!-- Mobile Filter Toggle -->
    <div class="mobile-filter-toggle">
      <button class="filter-toggle-btn" id="filterToggleBtn">Show Filters</button>
    </div>

    <!-- Sidebar -->
    <aside class="kids-sidebar" id="sidebar">
      <div class="sidebar-section">
        <h3>Categories</h3>
        <ul class="category-list" id="categoryList"></ul>
      </div>

      <div class="sidebar-section">
        <h3>Price Range</h3>
        <div class="price-range">
          <input type="range" min="0" max="100000" value="100000" id="priceSlider" class="price-slider" />
          <div class="price-display" id="priceDisplay">₹0 - ₹100000</div>
        </div>
      </div>

      <div class="sidebar-section">
        <h3>Sort By</h3>
        <select class="sort-select" id="sortSelect">
          <option value="newest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="rating">Rating</option>
        </select>
      </div>
    </aside>

    <!-- Main -->
    <main class="kids-main" id="kidsMain">

      <!-- Products Header -->
      <div class="products-header">
        <h2 id="productsTitle">Kids Products ({{ count($products) }})</h2>
      </div>

      <div class="products-grid" id="productsGrid">
        @foreach($products as $product)
          @php
            // ✅ Image URL safe (same method)
            $imgUrl = null;
            if (!empty($product->image)) {
              $imgUrl = \Illuminate\Support\Facades\Storage::url($product->image);
            }
            $finalImg = $imgUrl ?: 'https://placehold.co/600x600?text=No+Image';
          @endphp

          <article class="products-card"
            data-category="kids"
            data-id="{{ $product->id }}"
            data-name="{{ e($product->name) }}"
            data-price="{{ (float)($product->price ?? 0) }}"
            data-image="{{ $finalImg }}"
          >

            <!-- ✅ Only this area clickable -->
            <a href="/product/{{ $product->id }}" class="product-link">

              <div class="product-image-wrapper">
                <img
                  src="{{ $finalImg }}"
                  alt="{{ $product->name }}"
                  class="products-image"
                  loading="lazy"
                  onerror="this.onerror=null;this.src='https://placehold.co/600x600?text=No+Image';"
                />

                @if(!empty($product->badge))
                  <div class="products-badge">{{ $product->badge }}</div>
                @endif

                <button class="wishlist-btn" type="button" aria-label="Add to wishlist">♡</button>
              </div>

              <div class="product-info">
                <div class="product-rating">
                  <span class="star filled">⭐</span>
                  <span class="star filled">⭐</span>
                  <span class="star filled">⭐</span>
                  <span class="star filled">⭐</span>
                  <span class="star filled">⭐</span>
                  <span class="rating-value">(5.0)</span>
                </div>

                <h3 class="product-name" title="{{ $product->name }}">{{ $product->name }}</h3>

                <!-- ✅ Divider line like womens/mens -->
                <div class="divider-line"></div>

                <div class="product-pricing">
                  <span class="currents-price">₹{{ number_format((float)($product->price ?? 0), 2) }}</span>

                  @if(!empty($product->original_price) && (float)$product->original_price > (float)($product->price ?? 0))
                    <span class="original-price">₹{{ number_format((float)$product->original_price, 2) }}</span>
                    <span class="discount-percent">
                      {{ number_format((((float)$product->original_price - (float)($product->price ?? 0)) / (float)$product->original_price) * 100, 0) }}% OFF
                    </span>
                  @endif
                </div>
              </div>
            </a>

            <!-- ✅ Button outside link (card same) -->
            <div class="product-actions">
              <button class="add-to-cart-btn" type="button">Add to Cart</button>
            </div>

          </article>
        @endforeach

        @if(count($products) == 0)
          <p class="no-products">No kids products found.</p>
        @endif
      </div>

      <p class="no-products" id="noProducts" style="display:none;">No kids products found.</p>
    </main>

  </div>
</div>

<style>
/* ✅ SAME CARD STYLE (womens/mens) */

/* Grid like shop/womens (avoid huge cards) */
.kids-page .products-grid{
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 22px;
}

/* keep same card width */
.kids-page .products-card{
  max-width: 320px;
  width: 100%;
  justify-self: center;
  background:#fff;
  border-radius: 18px;
  overflow:hidden;
  box-shadow: 0 10px 26px rgba(0,0,0,0.08);
  border: 1px solid rgba(0,0,0,0.06);
  transition: transform .18s ease, box-shadow .18s ease;
}
.kids-page .products-card:hover{
  transform: translateY(-3px);
  box-shadow: 0 14px 30px rgba(0,0,0,0.10);
}

@media (max-width: 1100px){
  .kids-page .products-grid{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 820px){
  .kids-page .products-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 480px){
  .kids-page .products-grid{ grid-template-columns: 1fr; }
  .kids-page .products-card{ max-width: 360px; }
}

/* link */
.kids-page .product-link{
  text-decoration:none;
  color:inherit;
  display:block;
}

/* image */
.kids-page .product-image-wrapper{
  position:relative;
  width:100%;
  aspect-ratio: 1 / 1;
  background:#f2f3f7;
  overflow:hidden;
}
.kids-page .products-image{
  width:100%;
  height:100%;
  object-fit: cover;
  display:block;
}

/* badge */
.kids-page .products-badge{
  position:absolute;
  left: 12px;
  top: 12px;
  background: #111;
  color:#fff;
  font-size: 11px;
  font-weight: 800;
  padding: 6px 10px;
  border-radius: 999px;
  z-index: 2;
}

/* wishlist */
.kids-page .wishlist-btn{
  position:absolute;
  top: 12px;
  right: 12px;
  width: 38px;
  height: 38px;
  border-radius: 999px;
  border: 1px solid rgba(0,0,0,0.12);
  background: rgba(255,255,255,0.92);
  cursor:pointer;
  font-size: 18px;
  display:grid;
  place-items:center;
  z-index: 2;
}
.kids-page .wishlist-btn.active{
  background:#111;
  color:#fff;
  border-color: rgba(0,0,0,0.25);
}

/* info */
.kids-page .product-info{ padding: 14px 14px 10px; }
.kids-page .product-rating{ display:flex; align-items:center; gap:4px; margin-bottom:8px; }
.kids-page .star{ font-size: 13px; opacity: 0.35; }
.kids-page .star.filled{ opacity: 1; color:#ffb400; }
.kids-page .rating-value{ margin-left:6px; font-size:12px; font-weight:700; opacity:0.6; }

.kids-page .product-name{
  font-size: 15px;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 12px;
  display:-webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow:hidden;
}

/* divider */
.kids-page .divider-line{
  border-top: 1px dashed #f2b9c7;
  margin: 10px 0 12px;
}

/* pricing */
.kids-page .product-pricing{
  display:flex;
  align-items: baseline;
  gap: 10px;
  flex-wrap: wrap;
  padding-bottom: 10px;
}
.kids-page .currents-price{ font-size: 18px; font-weight: 900; color:#000; }
.kids-page .original-price{ font-size: 13px; font-weight: 800; opacity: 0.55; text-decoration: line-through; }
.kids-page .discount-percent{
  font-size: 11px;
  font-weight: 900;
  padding: 4px 10px;
  border-radius: 999px;
  border: 1px solid rgba(0,0,0,0.10);
  background: #f1f2f6;
  color: #16a34a;
}

/* button */
.kids-page .product-actions{
  padding: 0 14px 16px;
}
.kids-page .add-to-cart-btn{
  width:100%;
  border:0;
  padding: 14px 12px;
  border-radius: 14px;
  cursor:pointer;
  font-weight: 900;
  background:#111;
  color:#fff;
  transition: transform .15s ease;
}
.kids-page .add-to-cart-btn:hover{ transform: translateY(-1px); }

/* optional notification style (your old one) */
.cart-success-notification{
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #111;
  color: #fff;
  padding: 14px 16px;
  border-radius: 14px;
  box-shadow: 0 10px 25px rgba(0,0,0,.18);
  z-index: 9999;
  transition: opacity .25s ease;
}
.notification-content{ display:flex; align-items:center; gap:10px; }
.success-icon{
  width:26px;height:26px;border-radius:999px;background:#16a34a;
  display:flex;align-items:center;justify-content:center;font-weight:900;
}
.success-text{ font-weight: 800; font-size: 13px; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

  // Mobile filter toggle
  const filterToggleBtn = document.getElementById("filterToggleBtn");
  const sidebar = document.getElementById("sidebar");

  if (filterToggleBtn && sidebar) {
    filterToggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("show");
      filterToggleBtn.textContent = sidebar.classList.contains("show") ? "Hide Filters" : "Show Filters";
    });
  }

  // ✅ Single delegated click (no double listeners)
  document.addEventListener("click", function(e){

    // Add to cart
    if (e.target.classList.contains("add-to-cart-btn")) {
      e.preventDefault();
      e.stopPropagation();

      const card = e.target.closest(".products-card");
      const product = {
        id: card.dataset.id || Date.now(),
        name: card.dataset.name || "Product",
        price: parseFloat(card.dataset.price || 0),
        image: card.dataset.image || "",
        category: "kids",
        qty: 1
      };

      addToCart(product);
      showNotification(`${product.name} added to cart!`);
      window.location.href = "/cart";
    }

    // Wishlist
    if (e.target.classList.contains("wishlist-btn")) {
      e.preventDefault();
      e.stopPropagation();

      const card = e.target.closest(".products-card");
      const product = {
        id: card.dataset.id || Date.now(),
        name: card.dataset.name || "Product",
        price: parseFloat(card.dataset.price || 0),
        image: card.dataset.image || "",
        category: "kids"
      };

      toggleWishlist(product, e.target);
    }
  });

  function addToCart(product) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    const idx = cart.findIndex(item => item.id == product.id);

    if (idx > -1) cart[idx].qty += 1;
    else cart.push(product);

    localStorage.setItem("cart", JSON.stringify(cart));
    window.dispatchEvent(new CustomEvent('cartUpdated'));
  }

  function toggleWishlist(product, btnElement) {
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    const idx = wishlist.findIndex(item => item.id == product.id);

    if (idx > -1) {
      wishlist.splice(idx, 1);
      btnElement.classList.remove("active");
      btnElement.textContent = "♡";
    } else {
      wishlist.push(product);
      btnElement.classList.add("active");
      btnElement.textContent = "♥";
    }

    localStorage.setItem("wishlist", JSON.stringify(wishlist));
    window.dispatchEvent(new CustomEvent('wishlistUpdated'));
  }

  function showNotification(text) {
    const notif = document.createElement("div");
    notif.className = "cart-success-notification";
    notif.innerHTML = `
      <div class="notification-content">
        <div class="success-icon">✓</div>
        <div class="success-text">${text}</div>
      </div>`;
    document.body.appendChild(notif);

    setTimeout(() => (notif.style.opacity = "0"), 1700);
    setTimeout(() => notif.remove(), 2000);
  }
});
</script>

@include('footer')
@endsection
