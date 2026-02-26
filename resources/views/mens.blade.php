@extends('layout.master')

@section('body')

<div class="mens-page">

  <!-- HERO -->
  <section class="mens-hero">
    <div class="hero-overlay">
      <h1>Men's Fashion Collection</h1>
      <p>Stylish, durable, and comfortable outfits for every occasion</p>

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
  <div class="mens-container">

    <!-- Mobile Filter Toggle -->
    <div class="mobile-filter-toggle">
      <button class="filter-toggle-btn" id="filterToggleBtn">Show Filters</button>
    </div>

    <!-- Sidebar -->
    <aside class="mens-sidebar" id="sidebar">
      <div class="sidebar-section">
        <h3>Categories</h3>
        <ul class="subcategory-list underline-style" id="subcategoryList"></ul>
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
    <main class="mens-main" id="mensMain">

      <!-- Products Header -->
      <div class="products-header">
        <h2 id="productsTitle">Men's Products ({{ count($products) }})</h2>
      </div>

      <div class="products-grid" id="productsGrid">
        @foreach($products as $product)
          @php
            $subcat = !empty($product->subcategory) ? strtolower(trim($product->subcategory)) : 'uncategorized';

            $imgUrl = null;
            if (!empty($product->image)) {
              $imgUrl = \Illuminate\Support\Facades\Storage::url($product->image);
            }
            $finalImg = $imgUrl ?: 'https://placehold.co/600x600?text=No+Image';
          @endphp

          <article class="products-card"
            data-category="mens"
            data-subcategory="{{ $subcat }}"
            data-id="{{ $product->id }}"
            data-name="{{ e($product->name) }}"
            data-price="{{ (float)($product->price ?? 0) }}"
            data-image="{{ $finalImg }}"
          >

            <!-- Only this area clickable -->
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

            <!-- Button outside link -->
            <div class="product-actions">
              <button class="add-to-cart-btn" type="button">Add to Cart</button>
            </div>

          </article>
        @endforeach

        @if(count($products) == 0)
          <p class="no-products">No men's products found.</p>
        @endif
      </div>

      <p class="no-products" id="noProducts" style="display:none;">No men's products found.</p>
    </main>

  </div>
</div>

<style>
/* Grid */
.mens-page .products-grid{
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 22px;
}

/* card */
.mens-page .products-card{
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
.mens-page .products-card:hover{
  transform: translateY(-3px);
  box-shadow: 0 14px 30px rgba(0,0,0,0.10);
}

@media (max-width: 1100px){
  .mens-page .products-grid{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 820px){
  .mens-page .products-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 480px){
  .mens-page .products-grid{ grid-template-columns: 1fr; }
  .mens-page .products-card{ max-width: 360px; }
}

/* link */
.mens-page .product-link{
  text-decoration:none;
  color:inherit;
  display:block;
}

/* image */
.mens-page .product-image-wrapper{
  position:relative;
  width:100%;
  aspect-ratio: 1 / 1;
  background:#f2f3f7;
  overflow:hidden;
}
.mens-page .products-image{
  width:100%;
  height:100%;
  object-fit: cover;
  display:block;
}

/* badge */
.mens-page .products-badge{
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
.mens-page .wishlist-btn{
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
.mens-page .wishlist-btn.active{
  background:#111;
  color:#fff;
  border-color: rgba(0,0,0,0.25);
}

/* info */
.mens-page .product-info{ padding: 14px 14px 10px; }
.mens-page .product-rating{ display:flex; align-items:center; gap:4px; margin-bottom:8px; }
.mens-page .star{ font-size: 13px; opacity: 0.35; }
.mens-page .star.filled{ opacity: 1; color:#ffb400; }
.mens-page .rating-value{ margin-left:6px; font-size:12px; font-weight:700; opacity:0.6; }

.mens-page .product-name{
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
.mens-page .divider-line{
  border-top: 1px dashed #f2b9c7;
  margin: 10px 0 12px;
}

/* pricing */
.mens-page .product-pricing{
  display:flex;
  align-items: baseline;
  gap: 10px;
  flex-wrap: wrap;
  padding-bottom: 10px;
}
.mens-page .currents-price{ font-size: 18px; font-weight: 900; color:#000; }
.mens-page .original-price{ font-size: 13px; font-weight: 800; opacity: 0.55; text-decoration: line-through; }
.mens-page .discount-percent{
  font-size: 11px;
  font-weight: 900;
  padding: 4px 10px;
  border-radius: 999px;
  border: 1px solid rgba(0,0,0,0.10);
  background: #f1f2f6;
  color: #16a34a;
}

/* button */
.mens-page .product-actions{ padding: 0 14px 16px; }
.mens-page .add-to-cart-btn{
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
.mens-page .add-to-cart-btn:hover{ transform: translateY(-1px); }

/* categories underline list */
.mens-page .subcategory-list.underline-style{
  list-style: none;
  padding: 0;
  margin: 0;
}
.mens-page .subcategory-list.underline-style li{
  border-bottom: 1px solid rgba(13, 27, 62, 0.18);
}
.mens-page .subcategory-btn{
  width: 100%;
  text-align: left;
  background: transparent;
  border: 0;
  padding: 14px 6px;
  cursor: pointer;
  font-weight: 700;
  font-size: 14px;
  color: #0b1a3a;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.mens-page .subcategory-btn:hover{ background: rgba(0,0,0,0.03); }
.mens-page .subcategory-count{
  font-size: 12px;
  font-weight: 700;
  opacity: .75;
}

/* Mobile sidebar show/hide */
@media (max-width: 992px){
  .mens-sidebar{ display:none; }
  .mens-sidebar.show{ display:block; }
}

/* notification */
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

  const productsGrid = document.getElementById("productsGrid");
  const productsTitle = document.getElementById("productsTitle");
  const noProducts = document.getElementById("noProducts");

  const subcategoryList = document.getElementById("subcategoryList");
  const priceSlider = document.getElementById("priceSlider");
  const priceDisplay = document.getElementById("priceDisplay");
  const sortSelect = document.getElementById("sortSelect");

  const shopNowBtn = document.getElementById("shopNowBtn");
  const newArrivalsBtn = document.getElementById("newArrivalsBtn");

  // Mobile filter toggle
  const filterToggleBtn = document.getElementById("filterToggleBtn");
  const sidebar = document.getElementById("sidebar");
  if (filterToggleBtn && sidebar) {
    filterToggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("show");
      filterToggleBtn.textContent = sidebar.classList.contains("show") ? "Hide Filters" : "Show Filters";
    });
  }

  const cards = Array.from(document.querySelectorAll(".products-card"));

  function getSubcategoryCounts() {
    const counts = { all: cards.length };
    cards.forEach(c => {
      const subcat = (c.dataset.subcategory || "uncategorized").toLowerCase();
      counts[subcat] = (counts[subcat] || 0) + 1;
    });
    return counts;
  }

  function renderCategories() {
    if (!subcategoryList) return;

    const counts = getSubcategoryCounts();
    const allSubcats = Object.keys(counts)
      .filter(k => !["all", "uncategorized"].includes(k))
      .sort();

    const order = ["all", ...allSubcats];

    subcategoryList.innerHTML = order
      .filter(k => counts[k] && counts[k] > 0)
      .map(k => {
        const label = (k === "all") ? "All" : k.charAt(0).toUpperCase() + k.slice(1);
        const active = (k === "all") ? "active" : "";
        return `
          <li class="${active}" data-filter="${k}">
            <button type="button" class="subcategory-btn">
              <span>${label}</span>
              <span class="subcategory-count">${counts[k]}</span>
            </button>
          </li>
        `;
      }).join("");
  }

  let activeSubcategory = "all";
  let maxPrice = parseFloat(priceSlider?.value || 100000);
  let activeSort = sortSelect?.value || "newest";

  function applyFilters() {
    const filtered = cards.filter(card => {
      const subcat = (card.dataset.subcategory || "uncategorized").toLowerCase();
      const price = parseFloat(card.dataset.price || 0);

      const subcatOk = (activeSubcategory === "all") ? true : (subcat === activeSubcategory);
      const priceOk = price <= maxPrice;
      return subcatOk && priceOk;
    });

    const sorted = [...filtered].sort((a, b) => {
      const pa = parseFloat(a.dataset.price || 0);
      const pb = parseFloat(b.dataset.price || 0);
      const ida = parseInt(a.dataset.id || 0, 10);
      const idb = parseInt(b.dataset.id || 0, 10);

      if (activeSort === "price-low") return pa - pb;
      if (activeSort === "price-high") return pb - pa;
      if (activeSort === "oldest") return ida - idb;
      if (activeSort === "rating") return 0;
      return idb - ida;
    });

    cards.forEach(c => (c.style.display = "none"));
    sorted.forEach(c => {
      c.style.display = "";
      productsGrid.appendChild(c);
    });

    const count = sorted.length;
    if (productsTitle) productsTitle.textContent = `Men's Products (${count})`;
    if (noProducts) noProducts.style.display = count === 0 ? "block" : "none";
  }

  document.addEventListener("click", function(e) {
    const li = e.target.closest("#subcategoryList li");
    if (!li) return;

    activeSubcategory = (li.dataset.filter || "all").toLowerCase();

    document.querySelectorAll("#subcategoryList li").forEach(x => x.classList.remove("active"));
    li.classList.add("active");

    applyFilters();
  });

  if (priceSlider && priceDisplay) {
    const updatePrice = () => {
      maxPrice = parseFloat(priceSlider.value || 100000);
      priceDisplay.textContent = `₹0 - ₹${maxPrice}`;
      applyFilters();
    };
    priceSlider.addEventListener("input", updatePrice);
    updatePrice();
  }

  if (sortSelect) {
    sortSelect.addEventListener("change", function() {
      activeSort = sortSelect.value;
      applyFilters();
    });
  }

  if (shopNowBtn) {
    shopNowBtn.addEventListener("click", function() {
      document.getElementById("productsGrid")?.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  }

  if (newArrivalsBtn) {
    newArrivalsBtn.addEventListener("click", function() {
      if (sortSelect) sortSelect.value = "newest";
      activeSort = "newest";
      applyFilters();
      document.getElementById("productsGrid")?.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  }

  document.addEventListener("click", function(e){

    if (e.target.classList.contains("add-to-cart-btn")) {
      e.preventDefault();
      e.stopPropagation();

      const card = e.target.closest(".products-card");
      const product = {
        id: card.dataset.id || Date.now(),
        name: card.dataset.name || "Product",
        price: parseFloat(card.dataset.price || 0),
        image: card.dataset.image || "",
        category: "mens",
        qty: 1
      };

      addToCart(product);
      showNotification(`${product.name} added to cart!`);
      window.location.href = "/cart";
    }

    if (e.target.classList.contains("wishlist-btn")) {
      e.preventDefault();
      e.stopPropagation();

      const card = e.target.closest(".products-card");
      const product = {
        id: card.dataset.id || Date.now(),
        name: card.dataset.name || "Product",
        price: parseFloat(card.dataset.price || 0),
        image: card.dataset.image || "",
        category: "mens"
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

  renderCategories();
  applyFilters();

});
</script>

@include('footer')
@endsection
