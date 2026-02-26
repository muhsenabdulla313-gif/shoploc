<section class="nm-products" id="newProductsSection">
  <div class="nm-container">

    <!-- Header -->
    <header class="nm-header">
      <br>
      <h2 class="nm-title">New Arrivals</h2>
    </header>

    <!-- Grid -->
    <div class="nm-grid">
      @forelse($products as $product)
        @php
          $catRaw = strtolower(trim($product->category ?? 'all'));
          $cat = 'all';
          if (str_contains($catRaw, 'women')) $cat = 'women';
          elseif (str_contains($catRaw, 'men')) $cat = 'men';
          elseif (str_contains($catRaw, 'kids') || str_contains($catRaw, 'kid')) $cat = 'kids';

          $img = $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/800x1000?text=No+Image';

          $price = (float)($product->price ?? 0);
          $oprice = (float)($product->original_price ?? 0);
          $hasDiscount = ($oprice > 0 && $oprice > $price);

          $qty = (int)($product->stock ?? $product->qty ?? 1);
          $isSoldOut = ($qty <= 0) || (strtolower(trim($product->badge ?? '')) === 'sold out');

          // ✅ DB shipping charge
          $ship = (float)($product->shipping_charge ?? 0);
        @endphp

        <article class="nm-card"
          data-category="{{ $cat }}"
          data-href="/product/{{ $product->id }}"
          data-id="{{ $product->id }}"
          data-shipping="{{ $ship }}"  {{-- ✅ ADD --}}
        >
          <div class="nm-imgWrap">
            <img src="{{ $img }}" alt="{{ $product->name }}" class="nm-img" loading="lazy"
                 onerror="this.onerror=null;this.src='https://placehold.co/800x1000?text=No+Image';">

            @if($isSoldOut)
              <span class="nm-soldout">SOLD OUT</span>
            @endif

            @if(!empty($product->badge) && strtolower(trim($product->badge)) !== 'sold out')
              <span class="nm-badge">{{ $product->badge }}</span>
            @endif

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

            <div class="nm-actions">
              <button class="nm-cart" type="button" {{ $isSoldOut ? 'disabled' : '' }}>
                {{ $isSoldOut ? 'Sold Out' : 'Add to Cart' }}
              </button>
              <a class="nm-view" href="/product/{{ $product->id }}">View</a>
            </div>
          </div>
        </article>

      @empty
        <p class="nm-empty">No products found.</p>
      @endforelse
    </div>
  </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const section = document.getElementById("newProductsSection");
  if (!section) return;

  const cards = Array.from(section.querySelectorAll(".nm-card"));

  // ===== Helpers =====
  function getWishlist(){
    try { return JSON.parse(localStorage.getItem("wishlist")) || []; }
    catch(e){ return []; }
  }
  function setWishlist(list){
    localStorage.setItem("wishlist", JSON.stringify(list));
    window.dispatchEvent(new CustomEvent("wishlistUpdated"));
  }

  function getCart(){
    try { return JSON.parse(localStorage.getItem("cart")) || []; }
    catch(e){ return []; }
  }
  function setCart(list){
    localStorage.setItem("cart", JSON.stringify(list));
    window.dispatchEvent(new CustomEvent("cartUpdated"));
  }

  // ✅ Restore wishlist UI after refresh
  function restoreWishlistUI(){
    const wishlist = getWishlist();
    const ids = new Set(wishlist.map(x => String(x.id)));

    cards.forEach(card => {
      const id = String(card.dataset.id || "");
      const btn = card.querySelector(".nm-wish");
      if (!btn) return;

      if (ids.has(id)) {
        btn.classList.add("active");
        btn.textContent = "♥";
      } else {
        btn.classList.remove("active");
        btn.textContent = "♡";
      }
    });
  }

  // ✅ Card click -> product page (ignore buttons/links)
  cards.forEach(card => {
    card.addEventListener("click", (e) => {
      if (e.target.closest("button") || e.target.closest("a")) return;
      const href = card.getAttribute("data-href");
      if (href) window.location.href = href;
    });
  });

  // ✅ Image click -> product page (extra safe)
  section.querySelectorAll(".nm-img").forEach(img => {
    img.addEventListener("click", function(e){
      e.preventDefault();
      e.stopPropagation();
      const card = this.closest(".nm-card");
      const href = card?.getAttribute("data-href");
      if (href) window.location.href = href;
    });
  });

  // ===== Wishlist toggle (Category removed) =====
  section.querySelectorAll(".nm-wish").forEach(btn => {
    btn.addEventListener("click", function(e){
      e.preventDefault();
      e.stopPropagation();

      const card = this.closest(".nm-card");
      if (!card) return;

      const id = String(card.dataset.id || "");
      const name = card.querySelector(".nm-name")?.textContent?.trim() || "";
      const priceTxt = card.querySelector(".nm-price")?.textContent || "0";
      const price = parseFloat(priceTxt.replace(/[₹,]/g, "")) || 0;
      const img = card.querySelector(".nm-img")?.getAttribute("src") || "";

      // ✅ shipping charge from DB attribute
      const shipping_charge = parseFloat(card.dataset.shipping || "0") || 0;

      const product = {
        id,
        name,
        price,
        image: img.replace(window.location.origin, ""),
        shipping_charge
      };

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

  // ===== Cart =====
  section.querySelectorAll(".nm-cart").forEach(btn => {
    btn.addEventListener("click", function(e){
      e.preventDefault();
      e.stopPropagation();

      if (this.disabled) return;

      const card = this.closest(".nm-card");
      if (!card) return;

      const id = String(card.dataset.id || "");
      const name = card.querySelector(".nm-name")?.textContent?.trim() || "";
      const priceTxt = card.querySelector(".nm-price")?.textContent || "0";
      const price = parseFloat(priceTxt.replace(/[₹,]/g, "")) || 0;
      const img = card.querySelector(".nm-img")?.getAttribute("src") || "";

      // ✅ shipping charge from DB attribute
      const shipping_charge = parseFloat(card.dataset.shipping || "0") || 0;

      const product = {
        id,
        name,
        price,
        image: img.replace(window.location.origin, ""),
        shipping_charge,
        qty: 1
      };

      let cart = getCart();
      const idx = cart.findIndex(item => String(item.id) === String(product.id));

      if (idx > -1) {
        cart[idx].qty = (parseInt(cart[idx].qty || 1, 10) || 1) + 1;

        // ✅ safety: shipping_charge missing aanel set cheyyuka
        if (cart[idx].shipping_charge === undefined || cart[idx].shipping_charge === null) {
          cart[idx].shipping_charge = shipping_charge;
        }
      } else {
        cart.push(product);
      }

      setCart(cart);
      window.location.href = "/cart";
    });
  });

  // ✅ Initial restore
  restoreWishlistUI();
  window.addEventListener("wishlistUpdated", restoreWishlistUI);
});
</script>
