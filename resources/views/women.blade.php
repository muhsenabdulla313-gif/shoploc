@extends('layout.master')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/women.css') }}">
@endpush

@section('body')
<div class="womens-page">

  <!-- HERO -->
  <section class="womens-hero">
    <div class="hero-overlay">
      <h1>Fashion Collection</h1>
      <p>Trendy, elegant, and comfortable styles curated just for you</p>

      <div class="hero-buttons">
        <button class="hero-btn" id="shopNowBtn" type="button">Shop Now</button>
        <!-- <button class="hero-btn secondary" id="newArrivalsBtn" type="button">New Arrivals</button> -->
      </div>
    </div>
    <div class="hero-wave" aria-hidden="true"></div>
  </section>

  <!-- MAIN WRAP -->
  <div class="womens-wrap">

    <div class="womens-container">

      <!-- Mobile filter toggle -->
      <div class="mobile-filter-toggle">
        <button class="filter-toggle-btn" id="filterToggleBtn" type="button">Show Filters</button>
      </div>

      <!-- SIDEBAR -->
      <aside class="womens-sidebar" id="sidebar">
        <div class="sidebar-card">
          <h3 class="sidebar-title">CATEGORIES</h3>
          <ul class="subcategory-list" id="subcategoryList"></ul>
        </div>

        <div class="sidebar-card">
          <h3 class="sidebar-title">FILTER BY PRICE</h3>
          <div class="price-box">
            <div class="price-display" id="priceDisplay">Price: ₹0 - ₹100000</div>
            <input type="range" min="0" max="100000" value="100000" id="priceSlider" class="price-slider" />
            <!-- Filter button removed - filtering now happens automatically -->
          </div>
        </div>

        <div class="sidebar-card">
          <h3 class="sidebar-title">SORT BY</h3>
          <select class="sort-select" id="sortSelect">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
          </select>
        </div>
      </aside>

      <!-- MAIN -->
      <main class="womens-main" id="womensMain">

        <section class="products-section" id="newProductsSection">

          <!-- NEW ARRIVALS heading -->
          <!-- <div class="nm-head">
            <h2 class="nm-title">NEW ARRIVALS</h2>
          </div> -->

          <div class="products-grid nm-grid" id="productsGrid">

            @forelse($products as $product)
              @php
                $catSlug = \Illuminate\Support\Str::slug($product->category);

                $img = $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/800x1000?text=No+Image';

                $price  = (float)($product->price ?? 0);
                $oprice = (float)($product->original_price ?? 0);
                $hasDiscount = ($oprice > 0 && $oprice > $price);

                $qty = (int)($product->stock ?? $product->qty ?? 0);
                $isSoldOut = ($qty <= 0) || (strtolower(trim($product->badge ?? '')) === 'sold out');
              @endphp

              <article class="nm-card"
                data-category="{{ $catSlug }}"
                data-href="/product/{{ $product->id }}"
                data-id="{{ $product->id }}"
                data-price="{{ $price }}"
              >
                <div class="nm-imgWrap">
                  <img
                    src="{{ $img }}"
                    alt="{{ $product->name }}"
                    class="nm-img"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='https://placehold.co/800x1000?text=No+Image';"
                  >

                  <!-- wishlist icon -->
                  <button class="nm-wish" type="button" aria-label="Add to wishlist">♡</button>

                  @if($isSoldOut)
                    <span class="nm-soldout">SOLD OUT</span>
                  @endif
                </div>

                <div class="nm-info">
                  <h3 class="nm-name" title="{{ $product->name }}">{{ $product->name }}</h3>

                  <div class="nm-priceRow">
                    <span class="nm-price">₹{{ number_format($price, 2) }}</span>
                    @if($hasDiscount)
                      <span class="nm-oprice">₹{{ number_format($oprice, 2) }}</span>
                    @endif
                  </div>

                  {{-- ✅ Buttons removed (Add to Cart + View) --}}

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
  const section = document.getElementById("newProductsSection");
  if (!section) return;

  const productsGrid = document.getElementById("productsGrid");
  const noProducts = document.getElementById("noProducts");

  const subcategoryList = document.getElementById("subcategoryList");
  const priceSlider = document.getElementById("priceSlider");
  const priceDisplay = document.getElementById("priceDisplay");
  const sortSelect = document.getElementById("sortSelect");

  const filterToggleBtn = document.getElementById("filterToggleBtn");
  const sidebar = document.getElementById("sidebar");

  const shopNowBtn = document.getElementById("shopNowBtn");
  const newArrivalsBtn = document.getElementById("newArrivalsBtn");

  const dbCategories = @json($categories ?? []);

  if (filterToggleBtn && sidebar) {
    filterToggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("show");
      filterToggleBtn.textContent = sidebar.classList.contains("show") ? "Hide Filters" : "Show Filters";
    });
  }

  const cards = Array.from(section.querySelectorAll(".nm-card"));
  const norm = (s) => String(s || "").trim().toLowerCase().replace(/\s+/g, '-');

  // click whole card to open product (links/buttons exclude - now only wishlist button)
  cards.forEach(card => {
    card.addEventListener("click", function(e) {
      if (e.target.closest("button") || e.target.closest("a")) return;
      const href = this.dataset.href;
      if (href) window.location.href = href;
    });
  });

  function getCountsBySlug(){
    const counts = {};
    cards.forEach(c => {
      const slug = norm(c.dataset.category || "");
      if (!slug) return;
      counts[slug] = (counts[slug] || 0) + 1;
    });
    return counts;
  }

  let activeCategory = "all";
  let maxPrice = parseFloat(priceSlider?.value || 100000);
  let activeSort = sortSelect?.value || "newest";

  function renderCategoriesUI(categories){
    if (!subcategoryList) return;
    const counts = getCountsBySlug();
    const total = cards.length;

    let html = `
      <li class="active" data-filter="all">
        <button type="button" class="subcategory-btn">
          <span>All Products</span>
          <span class="subcategory-count">${total}</span>
        </button>
      </li>
    `;

    // Use server-provided categories if available, otherwise extract from product cards
    const categoriesToUse = (categories && categories.length > 0) ? categories : [...new Set(cards.map(card => card.dataset.category))];
    
    categoriesToUse.forEach(cat => {
      // Handle both server-provided categories (objects) and extracted names (strings)
      const catName = typeof cat === 'object' ? (cat.name || cat.category) : cat;
      if (!catName) return;
      const slug = norm(catName);
      const cnt = counts[slug] || 0;

      html += `
        <li data-filter="${slug}">
          <button type="button" class="subcategory-btn">
            <span>${catName}</span>
            <span class="subcategory-count">${cnt}</span>
          </button>
        </li>
      `;
    });

    subcategoryList.innerHTML = html;
  }

  function applyFilters() {
    const filtered = cards.filter(card => {
      const slug = norm(card.dataset.category || "");
      const price = parseFloat(card.dataset.price || 0);

      const catOk = (activeCategory === "all") ? true : (slug === activeCategory);
      const priceOk = price <= maxPrice;
      return catOk && priceOk;
    });

    const sorted = [...filtered].sort((a, b) => {
      const pa = parseFloat(a.dataset.price || 0);
      const pb = parseFloat(b.dataset.price || 0);
      const ida = parseInt(a.dataset.id || 0, 10);
      const idb = parseInt(b.dataset.id || 0, 10);

      if (activeSort === "price-low") return pa - pb;
      if (activeSort === "price-high") return pb - pa;
      if (activeSort === "oldest") return ida - idb;
      return idb - ida; // newest default
    });

    cards.forEach(c => (c.style.display = "none"));
    sorted.forEach(c => {
      c.style.display = "";
      productsGrid.appendChild(c);
    });

    if (noProducts) noProducts.style.display = sorted.length === 0 ? "block" : "none";
  }

  document.addEventListener("click", function(e) {
    const li = e.target.closest("#subcategoryList li");
    if (!li) return;

    activeCategory = norm(li.dataset.filter || "all");
    document.querySelectorAll("#subcategoryList li").forEach(x => x.classList.remove("active"));
    li.classList.add("active");
    applyFilters();
  });

  // price slider
  const priceFilterBtn = document.getElementById("priceFilterBtn");
  if (priceSlider && priceDisplay) {
    const updatePrice = () => {
      maxPrice = parseFloat(priceSlider.value || 100000);
      priceDisplay.textContent = `Price: ₹0 - ₹${maxPrice}`;
      // Apply filter automatically when slider moves
      applyFilters();
    };
    priceSlider.addEventListener("input", updatePrice);
    updatePrice();
  }
  // Remove the separate filter button click handler since filtering is now automatic
  /*
  if (priceFilterBtn) {
    priceFilterBtn.addEventListener("click", function(){
      applyFilters();
    });
  }
  */

  // sort
  if (sortSelect) {
    sortSelect.addEventListener("change", function() {
      activeSort = sortSelect.value;
      applyFilters();
    });
  }

  // hero buttons
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

  renderCategoriesUI(dbCategories);
  applyFilters();
});
</script>
@endpush
