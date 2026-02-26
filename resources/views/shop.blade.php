@extends('layout.master')

@section('title', 'Search Results')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/women.css') }}">
@endpush

@section('body')
@php
  $query = trim(request()->get('search',''));
  $queryLabel = $query !== '' ? $query : 'Search Results';
@endphp

<div class="womens-page">

  {{-- Small hero --}}
  <section class="womens-hero womens-hero--small">
    <div class="hero-overlay">
      <h1>Search Results</h1>
      <p><strong>{{ $queryLabel }}</strong></p>
    </div>
    <div class="hero-wave" aria-hidden="true"></div>
  </section>

  <div class="womens-wrap">
    <div class="womens-container">

      {{-- Mobile filter toggle --}}
      <div class="mobile-filter-toggle">
        <button class="filter-toggle-btn" id="filterToggleBtn" type="button">Show Filters</button>
      </div>

      {{-- SIDEBAR --}}
      <aside class="womens-sidebar" id="sidebar">

        {{-- Search term card --}}
        <div class="sidebar-card">
          <h3 class="sidebar-title">SEARCH</h3>
          <div class="sb-row">
            <div class="sb-query">{{ $queryLabel }}</div>
            <div class="sb-count">{{ count($products) }} items</div>
          </div>
        </div>

        <div class="sidebar-card">
          <h3 class="sidebar-title">CATEGORIES</h3>
          <ul class="subcategory-list underline-style" id="subcategoryList"></ul>
        </div>

        <div class="sidebar-card">
          <h3 class="sidebar-title">FILTER BY PRICE</h3>
          <div class="price-box">
            <div class="price-display" id="priceDisplay">Price: ₹0 - ₹100000</div>
            <input type="range" min="0" max="100000" value="100000" id="priceSlider" class="price-slider" />
            <button type="button" class="price-filter-btn" id="priceFilterBtn">FILTER</button>
          </div>
        </div>

        <div class="sidebar-card">
          <h3 class="sidebar-title">SORT BY</h3>
          <select class="sort-select" id="sortSelect">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="rating">Rating</option>
          </select>
        </div>
      </aside>

      {{-- MAIN --}}
      <main class="womens-main" id="womensMain">

        {{-- NEW ARRIVALS heading like image --}}
        <div class="nm-head">
          <!-- <h2 class="nm-title" id="productsTitle">NEW ARRIVALS ({{ count($products) }})</h2> -->
          </div>

        <section class="products-section" id="newProductsSection">
          <div class="products-grid nm-grid" id="productsGrid">

            @forelse($products as $product)
              @php
                $catRaw = strtolower(trim($product->category ?? 'all'));
                $cat = 'all';
                if (str_contains($catRaw, 'women')) $cat = 'women';
                elseif (str_contains($catRaw, 'men')) $cat = 'men';
                elseif (str_contains($catRaw, 'kids') || str_contains($catRaw, 'kid')) $cat = 'kids';

                $subcat = !empty($product->subcategory) ? strtolower(trim($product->subcategory)) : 'uncategorized';

                $img = $product->image
                  ? asset('storage/' . $product->image)
                  : 'https://placehold.co/800x1000?text=No+Image';

                $price  = (float)($product->price ?? 0);
                $oprice = (float)($product->original_price ?? 0);
                $hasDiscount = ($oprice > 0 && $oprice > $price);

                $qty = (int)($product->stock ?? $product->qty ?? 1);
                $isSoldOut = ($qty <= 0) || (strtolower(trim($product->badge ?? '')) === 'sold out');
              @endphp

              <article class="nm-card"
                data-category="{{ $cat }}"
                data-subcategory="{{ $subcat }}"
                data-id="{{ $product->id }}"
                data-price="{{ $price }}"
                data-href="/product/{{ $product->id }}"
              >
                <div class="nm-imgWrap">
                  <img
                    src="{{ $img }}"
                    alt="{{ $product->name }}"
                    class="nm-img"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='https://placehold.co/800x1000?text=No+Image';"
                  >

                  @if($isSoldOut)
                    <span class="nm-soldout">SOLD OUT</span>
                  @endif

                  @if(!empty($product->badge) && strtolower(trim($product->badge)) !== 'sold out')
                    <span class="nm-badge">{{ $product->badge }}</span>
                  @endif

                  {{-- Wishlist --}}
                  <button class="nm-wish" type="button" aria-label="Add to wishlist">♡</button>
                </div>

                <div class="nm-info">
                  <h3 class="nm-name" title="{{ $product->name }}">{{ $product->name }}</h3>

                  <div class="nm-priceRow">
                    <span class="nm-price">₹{{ number_format($price, 2) }}</span>
                    @if($hasDiscount)
                      <span class="nm-oprice">₹{{ number_format($oprice, 2) }}</span>
                    @endif
                  </div>

                  {{-- ✅ Add to cart + view removed --}}
                </div>
              </article>

            @empty
              <p class="nm-empty">No products found.</p>
            @endforelse

          </div>

          <p class="no-products" id="noProducts" style="display:none;">No products found.</p>
        </section>
      </main>

    </div>
  </div>
</div>

@include('footer')
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
  const productsGrid = document.getElementById("productsGrid");
  const productsTitle = document.getElementById("productsTitle");
  const noProducts = document.getElementById("noProducts");

  const subcategoryList = document.getElementById("subcategoryList");
  const priceSlider = document.getElementById("priceSlider");
  const priceDisplay = document.getElementById("priceDisplay");
  const sortSelect = document.getElementById("sortSelect");

  const filterToggleBtn = document.getElementById("filterToggleBtn");
  const sidebar = document.getElementById("sidebar");

  if (filterToggleBtn && sidebar) {
    filterToggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("show");
      filterToggleBtn.textContent = sidebar.classList.contains("show") ? "Hide Filters" : "Show Filters";
    });
  }

  const section = document.getElementById("newProductsSection");
  if (!section) return;

  const cards = Array.from(section.querySelectorAll(".nm-card"));

  // ===== Wishlist storage =====
  function getWishlist(){
    try { return JSON.parse(localStorage.getItem("wishlist")) || []; }
    catch(e){ return []; }
  }
  function setWishlist(list){
    localStorage.setItem("wishlist", JSON.stringify(list));
    window.dispatchEvent(new CustomEvent("wishlistUpdated"));
  }

  function restoreWishlistUI(){
    const wishlist = getWishlist();
    const ids = new Set(wishlist.map(x => String(x.id)));
    cards.forEach(card => {
      const id = String(card.dataset.id || "");
      const btn = card.querySelector(".nm-wish");
      if (!btn) return;
      if (ids.has(id)) { btn.classList.add("active"); btn.textContent = "♥"; }
      else { btn.classList.remove("active"); btn.textContent = "♡"; }
    });
  }

  // Card click -> product page (wishlist button exclude)
  cards.forEach(card => {
    card.addEventListener("click", (e) => {
      if (e.target.closest("button") || e.target.closest("a")) return;
      const href = card.getAttribute("data-href");
      if (href) window.location.href = href;
    });
  });

  // Wishlist toggle
  section.querySelectorAll(".nm-wish").forEach(btn => {
    btn.addEventListener("click", function(e){
      e.preventDefault();
      e.stopPropagation();

      const card = this.closest(".nm-card");
      if (!card) return;

      const id = String(card.dataset.id || "");
      const name = card.querySelector(".nm-name")?.textContent?.trim() || "";
      const price = parseFloat(card.dataset.price || "0") || 0;
      const img = card.querySelector(".nm-img")?.getAttribute("src") || "";
      const category = card.getAttribute("data-category") || "all";

      const product = { id, name, price, image: img.replace(window.location.origin, ""), category };

      let wishlist = getWishlist();
      const idx = wishlist.findIndex(item => String(item.id) === id);

      if (idx > -1){
        wishlist.splice(idx, 1);
        this.classList.remove("active");
        this.textContent = "♡";
      } else {
        wishlist.push(product);
        this.classList.add("active");
        this.textContent = "♥";
      }
      setWishlist(wishlist);
    });
  });

  // ===== Sidebar filters =====
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
      .map((k, i) => {
        const label = (k === "all") ? "All" : (k.charAt(0).toUpperCase() + k.slice(1));
        const active = (i === 0) ? "active" : "";
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
      return idb - ida; // newest
    });

    cards.forEach(c => (c.style.display = "none"));
    sorted.forEach(c => {
      c.style.display = "";
      productsGrid.appendChild(c);
    });

    const count = sorted.length;
    if (productsTitle) productsTitle.textContent = `NEW ARRIVALS (${count})`;
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

  const priceFilterBtn = document.getElementById("priceFilterBtn");
  if (priceSlider && priceDisplay) {
    const updatePrice = () => {
      maxPrice = parseFloat(priceSlider.value || 100000);
      priceDisplay.textContent = `Price: ₹0 - ₹${maxPrice}`;
    };
    priceSlider.addEventListener("input", updatePrice);
    updatePrice();
  }
  if (priceFilterBtn) priceFilterBtn.addEventListener("click", applyFilters);

  if (sortSelect) {
    sortSelect.addEventListener("change", function() {
      activeSort = sortSelect.value;
      applyFilters();
    });
  }

  renderCategories();
  restoreWishlistUI();
  applyFilters();
  window.addEventListener("wishlistUpdated", restoreWishlistUI);
});
</script>
@endpush
